<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proceso;
use App\Models\ConceptoJuridico;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConceptoController extends Controller
{
    // ===============================
    // MÉTODOS PRINCIPALES
    // ===============================

    public function storeProceso(Request $request, $id)
    {
        // 🚫 VALIDAR SI EL PROCESO ESTÁ ARCHIVADO
        $proceso = Proceso::findOrFail($id);

        if ($proceso->estado === 'Archivado') {
            return redirect()->back()->with('error', 'No se puede crear un concepto jurídico para un proceso archivado.');
        }

        // Validación
        $validated = $request->validate([
            'titulo' => 'required|string|max:120',
            'concepto' => 'required|string|min:50',
        ]);

        // Crear concepto relacionado al proceso
        $concepto = new ConceptoJuridico();
        $concepto->proceso_id = $id;
        $concepto->abogado_id = Auth::id();
        $concepto->titulo = $validated['titulo'];
        $concepto->descripcion = $validated['concepto'];
        $concepto->save();

        // 👇 CAMBIAR ESTA LÍNEA
        return redirect()->route('procesos.conceptos', $id)
            ->with('success', 'Concepto jurídico creado correctamente.');
    }

    public function index(Request $request)
    {
        try {
            // Iniciar query builder
            $query = Proceso::query();
            $searchTerm = $request->get('search');

            // Aplicar búsqueda si existe el término de búsqueda
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('id', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('estado', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('numero_radicado', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('tipo_proceso', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('demandante', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('demandado', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('created_at', 'LIKE', '%' . $searchTerm . '%');
                    // Agrega más campos según tu modelo Lawyer 
                });
            }

            // Obtener abogados paginados
            $procesos = $query->paginate(10);

            // Mantener parámetros de búsqueda en la paginación
            $procesos->appends($request->query());

            // Si es una petición AJAX, devolver solo la vista parcial
            if ($request->ajax()) {
                $html = view('profile.partials.lawyers-table', compact('lawyers'))->render();

                return response()->json([
                    'html' => $html,
                    'success' => true,
                    'total' => $procesos->total(),
                    'current_page' => $procesos->currentPage(),
                    'last_page' => $procesos->lastPage(),
                    'search_term' => $searchTerm
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error en ConceptoController@index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar los datos: ' . $e->getMessage()
                ], 500);
            }

            // Para peticiones normales, redirigir con error
            return back()->with('error', 'Error al cargar los datos');
        }
    }

    public function create(Request $request)
    {
        $query = Proceso::query();
        $abogadosList = collect(); // Lista vacía por defecto

        // --- Si el usuario es abogado ---
        if (Auth::user()->role_id == 2) {
            $lawyer = \App\Models\Lawyer::where('user_id', Auth::id())->first();
            if ($lawyer) {
                $query->where('lawyer_id', $lawyer->id);
            }
        }

        // --- Si el usuario es asistente o supervisor (role 3 o el que uses) ---
        if (Auth::user()->role_id == 3 || Auth::user()->role_id == 4) {

            $lawyerIds = collect();

            if (Auth::user()->role_id == 3) {
                $assistant = Auth::user()->assistant;
                if ($assistant) {
                    $lawyerIds = $assistant->lawyers()->pluck('lawyer_id');
                }
            } else {
                // Admin/supervisor ve todos los abogados
                $lawyerIds = \App\Models\Lawyer::pluck('id');
            }

            // Cargar lista de abogados para el select
            $abogadosList = \App\Models\Lawyer::with('user')
                ->whereIn('id', $lawyerIds)
                ->get();

            // Filtrar por abogado seleccionado
            if ($request->filled('abogado_id')) {
                $query->where('lawyer_id', $request->abogado_id);
            } else {
                $query->whereIn('lawyer_id', $lawyerIds);
            }
        }

        // --- BÚSQUEDA ---
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('numero_radicado', 'ILIKE', "%$search%")
                    ->orWhere('demandante', 'ILIKE', "%$search%")
                    ->orWhere('demandado', 'ILIKE', "%$search%")
                    ->orWhere('tipo_proceso', 'ILIKE', "%$search%")
                    ->orWhere('estado', 'ILIKE', "%$search%");
            });
        }

        $procesos = $query->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        // --- AJAX ---
        if ($request->ajax()) {
            $html = view('profile.partials.process-card', ['procesos' => $procesos])->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'total' => $procesos->count()
            ]);
        }

        return view('legal_processes.showConceptos', compact('procesos', 'abogadosList'));
    }

    /**
     * Mostrar el formulario para crear un concepto para un proceso específico
     */
    public function verFormulario($procesoId)
    {
        try {
            $proceso = $this->getProcesoWithRelations($procesoId);

            $this->checkExistingConcepto($procesoId);

            return view('legal_processes.showConceptos', compact('proceso'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de concepto', [
                'proceso_id' => $procesoId,
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Traer concepto con proceso y abogado (ajusta relaciones según tu modelo)
        $concepto = \App\Models\ConceptoJuridico::with(['proceso', 'abogado'])->findOrFail($id);

        return view('legal_processes.conceptos', compact('concepto'));

        return response()->json($concepto);
    }

    /**
     * Eliminar concepto específico
     */
    public function destroy($id)
    {
        $concepto = ConceptoJuridico::findOrFail($id);

        // Verificar que el usuario sea el propietario o un admin
        if ($concepto->abogado_id !== Auth::id() && Auth::user()->role_id != 1) {
            abort(403, 'No tienes permiso para eliminar este concepto.');
        }

        $concepto->delete();
        return redirect()->back()->with('success', 'Concepto jurídico eliminado correctamente.');
    }

    /**
     * Listar todos los conceptos asociados a un proceso
     */
    public function listByProceso($procesoId)
    {
        $proceso = Proceso::findOrFail($procesoId);

        $conceptos = ConceptoJuridico::with(['abogado'])->where('proceso_id', $procesoId)->get();

        return view('legal_processes.conceptos', compact('proceso', 'conceptos'));
    }

    // ===============================
    // MÉTODOS PRIVADOS DE APOYO
    // ===============================

    /**
     * Validar datos del concepto
     */
    private function validateConceptoData(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'descripcion' => 'required|min:50'
        ]);
    }

    /**
     * Obtener proceso con sus relaciones
     */
    private function getProcesoWithRelations($procesoId)
    {
        return Proceso::with(['cliente', 'abogado'])->findOrFail($procesoId);
    }

    /**
     * Verificar si ya existe un concepto para el proceso
     */
    private function checkExistingConcepto($procesoId)
    {
        $conceptoExistente = ConceptoJuridico::where('proceso_id', $procesoId)->first();

        if ($conceptoExistente) {
            abort(403, 'Ya existe un concepto para este proceso.');
        }
    }

    /**
     * Crear concepto jurídico para el proceso
     */
    private function createConceptoForProceso(Request $request, Proceso $proceso)
    {
        ConceptoJuridico::create([
            'proceso_id' => $proceso->id,
            'titulo' => $request->titulo,
            'categoria' => $request->categoria,
            'descripcion' => $request->descripcion,
            'abogado_id' => Auth::id()
        ]);
    }

    /**
     * Actualizar estado del proceso
     */
    private function updateProcesoEstado(Proceso $proceso)
    {
        $proceso->update(['estado' => 'con_concepto']);
    }

    // Método adicional para búsqueda rápida (opcional)
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->get('term');

            if (!$searchTerm) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'count' => 0
                ]);
            }

            $procesos = Proceso::where(function ($q) use ($searchTerm) {
                $q->where('id', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('numero_radicado', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('demandante', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('demandado', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('tipo_proceso', 'LIKE', '%' . $searchTerm . '%');
            })
                ->limit(20)
                ->get(['id', 'numero_radicado', 'demandante', 'demandado', 'estado']);

            return response()->json([
                'success' => true,
                'data' => $procesos,
                'count' => $procesos->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de procesos', [
                'term' => $searchTerm ?? 'N/A',
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }
}
