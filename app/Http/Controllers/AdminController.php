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

            // ============================
            // BUSCAR ABOGADOS
            // ============================
            $query = Lawyer::query();

            if ($searchTerm) {
                $searchTerms = explode(' ', $searchTerm); // separar por espacios

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
                            // Buscar en campos del asistente
                            $q2->where('nombre', 'ILIKE', "%$term%")
                                ->orWhere('apellido', 'ILIKE', "%$term%")
                                ->orWhere('tipo_documento', 'ILIKE', "%$term%")
                                ->orWhere('numero_documento', 'ILIKE', "%$term%")
                                ->orWhere('correo', 'ILIKE', "%$term%")
                                ->orWhere('telefono', 'ILIKE', "%$term%")
                                // Buscar en abogados asignados
                                ->orWhereHas('lawyers', function ($q3) use ($term) {
                                    $q3->where('nombre', 'ILIKE', "%$term%")
                                        ->orWhere('apellido', 'ILIKE', "%$term%");
                                });
                        });
                    }
                });
            }

            // ============================
            // PAGINACIONES
            // ============================
            // Obtener abogados paginados
            $lawyers = $query
                ->orderBy('id', 'asc') // o 'asc'
                ->paginate(10, ['*'], 'lawyersPage');


            // TABLA PEQUEÑA (mostrar todos)
            $lawyersSimple = Lawyer::orderBy('id', 'asc')
                ->paginate(10, ['*'], 'lawyersSimplePage');

            $assistants = $assistantQuery
                ->orderBy('id', 'asc')
                ->paginate(10, ['*'], 'assistantsPage');

            $assistantsSimple = Assistant::with('lawyers')
                ->orderBy('id', 'asc')
                ->paginate(10, ['*'], 'assistantsSimplePage');

            // PROCESOS (TABLA SIMPLE DASHBOARD)

            $procesosSimple = Proceso::with('lawyer')
                ->orderBy('id', 'asc')
                ->paginate(10, ['*'], 'procesosSimplePage');

            $abogados = Lawyer::all();

            // Mantener búsqueda en paginación
            foreach ([$lawyers, $assistants, $lawyersSimple, $assistantsSimple, $procesosSimple] as $p) {
                $p->appends(['search' => $searchTerm]);
            }

            // ============================
            // PETICIONES AJAX (BÚSQUEDA SIN RECARGAR)
            // ============================
            if ($request->ajax() && $request->has('search')) {

                $section = $request->get('section');

                if ($section === 'lawyers') {
                    return response()->json([
                        'success' => true,
                        'html' => view('profile.partials.lawyers-table', [
                            'lawyers' => $lawyers
                        ])->render()
                    ]);
                }

                if ($section === 'assistants') {
                    return response()->json([
                        'success' => true,
                        'html' => view('profile.partials.assistants-table', [
                            'assistants' => $assistants
                        ])->render()
                    ]);
                }
            }

            // ============================
            // PETICIONES AJAX (PAGINACIÓN)
            // ============================
            if ($request->ajax()) {
                if ($request->has('lawyersPage')) {
                    $html = view('profile.partials.lawyers-table', ['lawyers' => $lawyers])->render();
                } elseif ($request->has('lawyersSimplePage')) {
                    $html = view('profile.partials.lawyers-table-simple', ['lawyersSimple' => $lawyersSimple])->render();
                } elseif ($request->has('assistantsPage')) {
                    $html = view('profile.partials.assistants-table', ['assistants' => $assistants])->render();
                } elseif ($request->has('assistantsSimplePage')) {
                    $html = view('profile.partials.assistants-table-simple', ['assistantsSimple' => $assistantsSimple])->render();
                }
                // 👇 AÑADIR ESTO
                elseif ($request->has('procesosSimplePage')) {
                    $html = view('profile.partials.procesos-table-simple', [
                        'procesosSimple' => $procesosSimple
                    ])->render();
                }

                return response()->json(['html' => $html, 'success' => true]);
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
                'procesosSimple' // 👈 AÑADIDO
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
