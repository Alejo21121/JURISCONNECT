<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proceso;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\HistorialEstadoProceso;
use App\Models\ProcesoDocumento;





class LegalProcessController extends Controller
{
    // ===============================
    // CRUD BÁSICO
    // ===============================

    /**
     * Listar procesos judiciales
     */
    public function index(Request $request)
    {
        $query = Proceso::query()
            ->leftJoin('lawyers', 'procesos.lawyer_id', '=', 'lawyers.id')
            ->leftJoin('users', 'lawyers.user_id', '=', 'users.id')
            ->select('procesos.*');

        // --- Si el usuario es abogado (role_id = 2) ---
        if (Auth::user()->role_id == 2) {

            // buscar lawyer por user_id
            $lawyer = \App\Models\Lawyer::where('user_id', Auth::id())->first();

            if ($lawyer) {
                $query->where('lawyer_id', $lawyer->id);
            }
        }

        // --- Si el usuario es asistente (role_id = 3) ---
        if (Auth::user()->role_id == 3) {

            // 1. Obtener el asistente vinculado al usuario
            $assistant = Auth::user()->assistant; // relación user -> assistant

            // 2. Si existe, obtener los abogados vinculados
            if ($assistant) {
                $lawyerIds = $assistant->lawyers()->pluck('lawyer_id');

                // 3. Filtrar procesos de esos abogados
                $query->whereIn('lawyer_id', $lawyerIds);
            }
        }
        // --- BÚSQUEDA ---
        if ($request->has('search') && $request->get('search')) {
            $search = $request->get('search');

            $query->where(function ($q) use ($search) {
                $q->where('procesos.numero_radicado', 'ILIKE', "%$search%")
                    ->orWhere('procesos.demandante', 'ILIKE', "%$search%")
                    ->orWhere('procesos.demandado', 'ILIKE', "%$search%")
                    ->orWhere('procesos.tipo_proceso', 'ILIKE', "%$search%")
                    ->orWhere('procesos.estado', 'ILIKE', "%$search%")

                    // ⭐ AHORA SÍ BUSCA POR EL ABOGADO
                    ->orWhere('users.name', 'ILIKE', "%{$search}%")
                    ->orWhere('users.email', 'ILIKE', "%{$search}%");
            });
        }

        $procesos = $query->orderBy('id', 'asc')->paginate(10);

        // --- AJAX ---
        if ($request->ajax() || $request->get('ajax')) {
            $html = view('profile.partials.processes-table', ['procesos' => $procesos])->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'total' => $procesos->total()
            ]);
        }

        return view('legal_processes.index', compact('procesos'));
    }

    /**
     * Mostrar formulario para crear proceso judicial
     */
    public function create()
    {
        // ❌ SOLO ABOGADO (role_id = 2) puede crear
        if (Auth::user()->role_id != 2) {
            abort(403, 'No tienes permisos para crear procesos.');
        }

        return view('legal_processes.create');
    }

    public function store(Request $request)
    {
        // ❌ SOLO ABOGADO (role_id = 2) puede crear
        if (Auth::user()->role_id != 2) {
            abort(403, 'No tienes permisos para crear procesos.');
        }

        try {

            $validated = $this->validateProcesoData($request);

            // Estado por defecto
            $validated['estado'] = 'Pendiente';

            // ✔️ Guardar el usuario que creó el proceso
            $validated['user_id'] = Auth::id();


            // Normalizar pago
            $validated['requiere_pago'] = (int) $request->requiere_pago;

            if ($validated['requiere_pago'] === 0) {
                $validated['valor_estimado'] = null;
            }

            // ✔️ Obtener el abogado REAL del usuario
            // (de la tabla lawyers)
            $lawyer = Auth::user()->lawyer;

            if (!$lawyer) {
                throw new \Exception("El usuario no tiene un abogado asociado en la tabla lawyers.");
            }

            // ✔️ Guardar ID del abogado correcto
            $validated['lawyer_id'] = $lawyer->id;

            // Crear proceso
            $proceso = Proceso::create($validated);

            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $file) {

                    $path = $file->store('documentos', 'public');

                    \App\Models\ProcesoDocumento::create([
                        'proceso_id' => $proceso->id,
                        'nombre' => $file->getClientOriginalName(),
                        'ruta' => $path,
                    ]);
                }
            }

            // 👇 HISTORIAL INICIAL
            HistorialEstadoProceso::create([
                'proceso_id' => $proceso->id,
                'estado' => 'Pendiente',
                'observacion' => 'Creación del proceso',
                'user_id' => Auth::id(),
            ]);

            // Respuesta AJAX
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proceso judicial creado con éxito.',
                    'data' => $proceso
                ], 201);
            }

            return redirect()
                ->route('abogado.dashboard')
                ->with('success', 'Proceso judicial creado con éxito.');
        } catch (\Illuminate\Validation\ValidationException $e) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        } catch (\Exception $e) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el proceso',
                    'error' => $e->getMessage()
                ], 500);
            }

            throw $e;
        }
    }

    /**
     * Mostrar un proceso específico
     */
    public function show($id)
    {
        $proceso = Proceso::with('documentos')->findOrFail($id);

        return response()->json($proceso);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $proceso = Proceso::findOrFail($id);
        return view('legal_processes.editProcesos', compact('proceso'));
    }

    /**
     * Actualizar proceso judicial
     */
    public function update(Request $request, $id)
    {
        $proceso = Proceso::findOrFail($id);

        $estadoAnterior = $proceso->estado;

        $validated = $this->validateProcesoDataForUpdate($request, $id);
        $this->removeAuxiliaryFields($validated);

        $validated['requiere_pago'] = (int) $request->requiere_pago;

        if ($validated['requiere_pago'] === 0) {
            $validated['valor_estimado'] = null;
        }

        $proceso->update($validated);

        // ✅ GUARDAR DOCUMENTOS NUEVOS
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {

                $path = $file->store('documentos', 'public');

                \App\Models\ProcesoDocumento::create([
                    'proceso_id' => $proceso->id,
                    'nombre' => $file->getClientOriginalName(),
                    'ruta' => $path,
                ]);
            }
        }

        // ✅ HISTORIAL
        if (isset($validated['estado']) && $validated['estado'] !== $estadoAnterior) {
            HistorialEstadoProceso::create([
                'proceso_id' => $proceso->id,
                'estado' => $validated['estado'],
                'observacion' => $request->observacion ?? 'Cambio de estado del proceso',
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('procesos.index')
            ->with('success', 'Proceso actualizado correctamente.');
    }

    /**
     * Eliminar proceso judicial
     */
    public function destroy($id)
    {
        $proceso = Proceso::findOrFail($id);
        $proceso->delete(); // cascade elimina documentos

        return redirect()
            ->route('mis.procesos')
            ->with('success', 'Proceso eliminado correctamente.');
    }

    public function destroyDocumento($id)
    {
        $doc = ProcesoDocumento::findOrFail($id);

        if (Storage::disk('public')->exists($doc->ruta)) {
            Storage::disk('public')->delete($doc->ruta);
        }

        $doc->delete();

        return response()->json([
            'success' => true
        ], 200);
    }
    // ===============================
    // MÉTODOS PRIVADOS
    // ===============================

    private function validateProcesoData(Request $request)
    {
        return $request->validate([
            'tipo_proceso'    => 'required|string|max:100',
            'numero_radicado' => 'required|string|max:50|unique:procesos,numero_radicado',
            'demandante'      => 'required|string|max:255',
            'demandado'       => 'required|string|max:255',
            'descripcion'     => 'required|string',

            // 👇 NUEVO
            'requiere_pago'   => 'required|boolean',
            'valor_estimado'  => 'nullable|required_if:requiere_pago,1|numeric|min:0',

            'estado'          => 'nullable|string',
            'documentos.*'    => 'file|mimes:pdf,doc,docx|max:10240',
        ]);
    }

    private function validateProcesoDataForUpdate(Request $request, $id)
    {
        return $request->validate([
            'tipo_proceso'    => 'required|string|max:100',
            'numero_radicado' => 'required|string|max:50|unique:procesos,numero_radicado,' . $id,
            'demandante'      => 'required|string|max:255',
            'demandado'       => 'required|string|max:255',
            'descripcion'     => 'required|string',
            'estado'          => 'nullable|string',

            // 👇 AÑADIR
            'requiere_pago'   => 'required|boolean',
            'valor_estimado'  => 'nullable|required_if:requiere_pago,1|numeric|min:0',

            'documentos.*'    => 'file|mimes:pdf,doc,docx|max:10240',
        ]);
    }


    public function historial($id)
    {
        $historial = \App\Models\HistorialEstadoProceso::with('user')
            ->where('proceso_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $historial
        ]);
    }

    private function deleteExistingDocument(Proceso $proceso)
    {
        if ($proceso->documento && Storage::disk('public')->exists($proceso->documento)) {
            Storage::disk('public')->delete($proceso->documento);
        }
    }

    private function deleteAssociatedDocument(Proceso $proceso)
    {
        if ($proceso->documento && Storage::disk('public')->exists($proceso->documento)) {
            Storage::disk('public')->delete($proceso->documento);
        }
    }

    private function removeAuxiliaryFields(array &$validated)
    {
        unset($validated['eliminar_documento']);
    }
}
