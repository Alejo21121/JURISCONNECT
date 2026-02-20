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
            $lawyer = \App\Models\Lawyer::where('user_id', Auth::id())->first();
            if ($lawyer) {
                $query->where('lawyer_id', $lawyer->id);
            }
        }

        // --- Si el usuario es asistente (role_id = 3) ---
        if (Auth::user()->role_id == 3) {
            $assistant = Auth::user()->assistant;
            if ($assistant) {
                $lawyerIds = $assistant->lawyers()->pluck('lawyer_id');
                $query->whereIn('lawyer_id', $lawyerIds);
            }
        }

        // --- BÚSQUEDA ---
        $search = $request->get('search', ''); // ⭐ Guardar el término

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('procesos.numero_radicado', 'ILIKE', "%$search%")
                    ->orWhere('procesos.demandante', 'ILIKE', "%$search%")
                    ->orWhere('procesos.demandado', 'ILIKE', "%$search%")
                    ->orWhere('procesos.tipo_proceso', 'ILIKE', "%$search%")
                    ->orWhere('procesos.estado', 'ILIKE', "%$search%")
                    ->orWhere('users.name', 'ILIKE', "%{$search}%")
                    ->orWhere('users.email', 'ILIKE', "%{$search}%");
            });
        }

        $procesos = $query
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->appends(['search' => $search]); // ⭐ AGREGAR ESTO en lugar de withQueryString()

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
        if (Auth::user()->role_id != 2) {
            abort(403, 'No tienes permisos para crear procesos.');
        }

        try {
            // 🔑 LIMPIAR ANTES DE VALIDAR
            if ($request->requiere_pago == 1 && $request->valor_estimado) {
                $request->merge([
                    'valor_estimado' => preg_replace('/\D/', '', $request->valor_estimado)
                ]);
            }

            // ✅ VALIDAR UNA SOLA VEZ
            $validated = $this->validateProcesoData($request);

            // 🔑 Agregar prefijo automáticamente
            $numeroRadicado = 'RAD-' . $validated['numero_radicado'];

            // Validar que no exista ya
            if (Proceso::where('numero_radicado', $numeroRadicado)->exists()) {
                return back()->withErrors([
                    'numero_radicado' => 'Este número de radicado ya existe.'
                ])->withInput();
            }

            $validated['numero_radicado'] = trim($validated['numero_radicado']);
            $validated['numero_radicado'] = $numeroRadicado;
            $validated['estado'] = 'Pendiente';
            $validated['user_id'] = Auth::id();
            $validated['requiere_pago'] = (int) $request->requiere_pago;

            if ($validated['requiere_pago'] === 0) {
                $validated['valor_estimado'] = null;
            }

            $lawyer = Auth::user()->lawyer;
            if (!$lawyer) {
                throw new \Exception("El usuario no tiene abogado asociado.");
            }

            $validated['lawyer_id'] = $lawyer->id;

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

            // 🔔 NOTIFICAR A LOS ASISTENTES DEL ABOGADO
            $nombreAbogado = $lawyer->nombre . ' ' . $lawyer->apellido;
            $asistentes = $lawyer->assistants; // Relación en el modelo Lawyer

            foreach ($asistentes as $asistente) {
                if ($asistente->user) {
                    $asistente->user->notify(
                        new \App\Notifications\NuevoProcesoRegistrado($proceso, $nombreAbogado)
                    );
                }
            }

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
        $proceso = Proceso::with(['documentos', 'pago'])->findOrFail($id); // 👈 Agregar 'pago'

        // Calcular porcentaje de pago
        $totalPagado = $proceso->cuotas->where('estado', 'Pagada')->sum('valor');
        $valorTotal = $proceso->valor_estimado ?? 0;
        $porcentaje = $valorTotal > 0 ? round(($totalPagado / $valorTotal) * 100, 2) : 0;

        return response()->json([
            'id' => $proceso->id,
            'numero_radicado' => $proceso->numero_radicado,
            'tipo_proceso' => $proceso->tipo_proceso,
            'demandante' => $proceso->demandante,
            'demandado' => $proceso->demandado,
            'descripcion' => $proceso->descripcion,
            'estado' => $proceso->estado,
            'requiere_pago' => $proceso->requiere_pago,
            'valor_estimado' => $proceso->valor_estimado,
            'fecha_vencimiento' => $proceso->fecha_vencimiento->format('d-m-Y'), // 👈 AGREGAR
            'created_at' => $proceso->created_at->format('d-m-Y'),
            'pago_realizado' => $proceso->pago !== null,
            'porcentaje' => $porcentaje, // 👈 AGREGAR ESTO
            'documentos' => $proceso->documentos,
        ]);
    }
    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $proceso = Proceso::findOrFail($id);

        // ✅ El proceso se considera pagado si tiene al menos una cuota
        $procesoPagado = $proceso->cuotas()->exists();

        return view('legal_processes.editProcesos', compact(
            'proceso',
            'procesoPagado'
        ));
    }

    /**
     * Actualizar proceso judicial
     */
    public function update(Request $request, $id)
    {
        $proceso = Proceso::findOrFail($id);
        $estadoAnterior = $proceso->estado;

        // 🔑 LIMPIAR ANTES DE VALIDAR
        if ($request->requiere_pago == 1 && $request->valor_estimado) {
            $request->merge([
                'valor_estimado' => preg_replace('/\D/', '', $request->valor_estimado)
            ]);
        }

        // ✅ VALIDAR UNA SOLA VEZ
        $validated = $this->validateProcesoDataForUpdate($request, $id);
        $this->removeAuxiliaryFields($validated);

        $validated['requiere_pago'] = (int) $request->requiere_pago;

        if ($validated['requiere_pago'] === 0) {
            $validated['valor_estimado'] = null;
        }

        // ===============================
        // TRASLADO DE PROCESO
        // ===============================
        if ($request->filled('nuevo_lawyer_id') && Auth::user()->role_id == 2) {

            // ❌ No permitir traslado si tiene pagos
            if ($proceso->pago()->exists()) {
                return back()->with('error', 'No se puede trasladar un proceso que ya tiene pagos registrados.');
            }

            $nuevoLawyer = \App\Models\Lawyer::find($request->nuevo_lawyer_id);

            if ($nuevoLawyer && $nuevoLawyer->id != $proceso->lawyer_id) {

                $lawyerActual = Auth::user()->lawyer;

                // Cambiar abogado y estado
                $proceso->lawyer_id = $nuevoLawyer->id;
                $proceso->estado = 'Traslado';
                $proceso->save();

                // Guardar historial
                HistorialEstadoProceso::create([
                    'proceso_id' => $proceso->id,
                    'estado' => 'Trasladado',
                    'observacion' => 'Proceso trasladado al abogado '
                        . $nuevoLawyer->nombre . ' ' . $nuevoLawyer->apellido,
                    'user_id' => Auth::id(),
                ]);

                // 🔔 Notificar nuevo abogado
                if ($nuevoLawyer->user) {

                    $nombreAbogado = Auth::user()->name;

                    $nuevoLawyer->user->notify(
                        new \App\Notifications\ProcesoTrasladadoNotification(
                            $proceso,
                            $nombreAbogado
                        )
                    );
                }

                return redirect()
                    ->route('procesos.index')
                    ->with('success', 'Proceso trasladado correctamente.');
            }
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
            'numero_radicado' => [
                'required',
                'regex:/^[0-9\-]+$/'
            ],
            'demandante'      => 'required|string|max:255',
            'demandado'       => 'required|string|max:255',
            'descripcion'     => 'required|string',
            'requiere_pago'   => 'required|boolean',
            'valor_estimado'  => 'nullable|required_if:requiere_pago,1|numeric|min:0',
            'fecha_vencimiento' => 'required|date|after_or_equal:today', // 👈 AGREGAR
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
            'requiere_pago'   => 'required|boolean',
            'valor_estimado'  => 'nullable|required_if:requiere_pago,1|numeric|min:0',
            'fecha_vencimiento' => 'required|date|after_or_equal:today', // 👈 AGREGAR
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

    public function reabrir($id)
    {
        $proceso = Proceso::findOrFail($id);

        // Cambiar estado
        $proceso->estado = 'Reabierto';
        $proceso->save();

        // Registrar en historial
        HistorialEstadoProceso::create([
            'proceso_id' => $proceso->id,
            'estado' => 'Reabierto',
            'observacion' => 'Proceso reabierto',
            'user_id' => Auth::id(),
        ]);

        return response()->json(['success' => true]);
    }
}
