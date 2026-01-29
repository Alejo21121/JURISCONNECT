<?php

namespace App\Http\Controllers;

use App\Models\Lawyer; // Asegúrate de importar el modelo
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Proceso;
use App\Models\Assistant;


class AdminController extends Controller
{
    public function index(Request $request)
    {
        try {
            $searchTerm = $request->get('search');
            $radicado = $request->get('radicado'); // 👈 Obtener término de búsqueda

            // ============================
            // BUSCAR ABOGADOS
            // ============================
            $query = Lawyer::query();

            if ($searchTerm) {
                $searchTerms = explode(' ', $searchTerm);
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->where(function ($q2) use ($term) {
                            $q2->where('nombre', 'ILIKE', "%$term%")
                                ->orWhere('apellido', 'ILIKE', "%$term%")
                                ->orWhere('tipo_documento', 'ILIKE', "%$term%")
                                ->orWhere('numero_documento', 'ILIKE', "%$term%")
                                ->orWhere('correo', 'ILIKE', "%$term%")
                                ->orWhere('telefono', 'ILIKE', "%$term%")
                                ->orWhere('especialidad', 'ILIKE', "%$term%");
                        });
                    }
                });
            }

            // ============================
            // BUSCAR ASISTENTES
            // ============================
            $assistantQuery = Assistant::with('lawyers');

            if ($searchTerm) {
                $searchTerms = explode(' ', $searchTerm);
                $assistantQuery->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->where(function ($q2) use ($term) {
                            $q2->where('nombre', 'ILIKE', "%$term%")
                                ->orWhere('apellido', 'ILIKE', "%$term%")
                                ->orWhere('tipo_documento', 'ILIKE', "%$term%")
                                ->orWhere('numero_documento', 'ILIKE', "%$term%")
                                ->orWhere('correo', 'ILIKE', "%$term%")
                                ->orWhere('telefono', 'ILIKE', "%$term%")
                                ->orWhereHas('lawyers', function ($q3) use ($term) {
                                    $q3->where('nombre', 'ILIKE', "%$term%")
                                        ->orWhere('apellido', 'ILIKE', "%$term%");
                                });
                        });
                    }
                });
            }
            // ============================
            // BUSCAR PROCESOS
            // ============================
            $procesosQuery = Proceso::with('lawyer');

            if ($request->filled('radicado')) {
                $radicado = $request->get('radicado');
                $procesosQuery->where(function ($q) use ($radicado) {
                    $q->where('numero_radicado', 'ILIKE', '%' . $radicado . '%')
                        ->orWhere('tipo_proceso', 'ILIKE', '%' . $radicado . '%')
                        ->orWhere('demandante', 'ILIKE', '%' . $radicado . '%')
                        ->orWhere('demandado', 'ILIKE', '%' . $radicado . '%')
                        ->orWhere('estado', 'ILIKE', '%' . $radicado . '%')
                        ->orWhereHas('lawyer', function ($q2) use ($radicado) {
                            $q2->where('nombre', 'ILIKE', '%' . $radicado . '%')
                                ->orWhere('apellido', 'ILIKE', '%' . $radicado . '%');
                        });
                });
            }

            $procesosSimple = $procesosQuery->orderBy('id', 'asc')->paginate(10, ['*'], 'procesosSimplePage');

            // ============================
            // PAGINACIONES
            // ============================
            $lawyers = $query->orderBy('id', 'asc')->paginate(10, ['*'], 'lawyersPage');
            $lawyersSimple = Lawyer::orderBy('id', 'asc')->paginate(10, ['*'], 'lawyersSimplePage');
            $assistants = $assistantQuery->orderBy('id', 'asc')->paginate(10, ['*'], 'assistantsPage');
            $assistantsSimple = Assistant::with('lawyers')->orderBy('id', 'asc')->paginate(10, ['*'], 'assistantsSimplePage');
            $procesosSimple = $procesosQuery->orderBy('id', 'asc')->paginate(10, ['*'], 'procesosSimplePage');

            $abogados = Lawyer::all();

            // Mantener búsqueda en paginación
            foreach ([$lawyers, $assistants, $lawyersSimple, $assistantsSimple, $procesosSimple] as $p) {
                $p->appends([
                    'search' => $searchTerm,
                    'radicado' => $radicado // 👈 Agregar radicado a la paginación
                ]);
            }

            // ============================
            // PETICIONES AJAX
            // ============================
            if ($request->ajax()) {
                // Búsqueda de abogados
                if ($request->has('search') && $request->get('section') === 'lawyers') {
                    return response()->json([
                        'success' => true,
                        'html' => view('profile.partials.lawyers-table', ['lawyers' => $lawyers])->render()
                    ]);
                }

                // Búsqueda de asistentes
                if ($request->has('search') && $request->get('section') === 'assistants') {
                    return response()->json([
                        'success' => true,
                        'html' => view('profile.partials.assistants-table', ['assistants' => $assistants])->render()
                    ]);
                }

                // 👇 BÚSQUEDA DE PROCESOS POR RADICADO
                if ($request->has('radicado') || $request->has('procesosSimplePage')) {
                    return response()->json([
                        'success' => true,
                        'html' => view('profile.partials.procesos-table-simple', [
                            'procesosSimple' => $procesosSimple
                        ])->render()
                    ]);
                }

                // Paginación
                if ($request->has('lawyersPage')) {
                    $html = view('profile.partials.lawyers-table', ['lawyers' => $lawyers])->render();
                } elseif ($request->has('lawyersSimplePage')) {
                    $html = view('profile.partials.lawyers-table-simple', ['lawyersSimple' => $lawyersSimple])->render();
                } elseif ($request->has('assistantsPage')) {
                    $html = view('profile.partials.assistants-table', ['assistants' => $assistants])->render();
                } elseif ($request->has('assistantsSimplePage')) {
                    $html = view('profile.partials.assistants-table-simple', ['assistantsSimple' => $assistantsSimple])->render();
                }

                return response()->json(['html' => $html ?? '', 'success' => true]);
            }

            // ============================
            // CONTADORES PARA DASHBOARD
            // ============================
            $totalLawyers = Lawyer::count();
            $cases_count = Proceso::count();
            $totalAsistentes = Assistant::count();

            return view('dashboard', compact(
                'lawyers',
                'totalLawyers',
                'abogados',
                'lawyersSimple',
                'cases_count',
                'totalAsistentes',
                'assistants',
                'assistantsSimple',
                'procesosSimple'
            ));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar los datos: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al cargar los datos');
        }
    }
}
