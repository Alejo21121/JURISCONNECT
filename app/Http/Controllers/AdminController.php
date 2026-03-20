<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Proceso;
use App\Models\Assistant;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // ============================
        // 👇 REDIRECCIÓN POR ROL
        // ============================
        $user = Auth::user();

        if ($user->role_id == 2) {
            return redirect()->route('dashboard.abogado');
        }

        if ($user->role_id == 3) {
            return redirect()->route('dashboard.asistente');
        }

        // Solo role_id == 1 (Admin) continúa aquí ↓

        try {
            $searchTerm = $request->get('search');
            $radicado = $request->get('radicado');

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

            // ── ORDENAMIENTO ABOGADOS ──
            $sortLawyer = $request->get('sort_lawyer', 'id');
            $dirLawyer  = $request->get('dir_lawyer', 'asc') === 'desc' ? 'desc' : 'asc';


            $lawyerColumns = [
                'nombre'           => 'nombre',
                'apellido'         => 'apellido',
                'tipo_documento'   => 'tipo_documento',
                'numero_documento' => 'numero_documento',
                'correo'           => 'correo',
                'telefono'         => 'telefono',
                'especialidad'     => 'especialidad',
                'id'               => 'id',
            ];

            $orderByLawyer = $lawyerColumns[$sortLawyer] ?? 'id';

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

            // ── ORDENAMIENTO ASISTENTES ──
            $sortAssistant = $request->get('sort_assistant', 'id');
            $dirAssistant  = $request->get('dir_assistant', 'asc') === 'desc' ? 'desc' : 'asc';

            $assistantColumns = [
                'nombre'           => 'nombre',
                'apellido'         => 'apellido',
                'tipo_documento'   => 'tipo_documento',
                'numero_documento' => 'numero_documento',
                'correo'           => 'correo',
                'telefono'         => 'telefono',
                'id'               => 'id',
            ];

            $orderByAssistant = $assistantColumns[$sortAssistant] ?? 'id';

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

            // ============================
            // PAGINACIONES
            // ============================
            $lawyers = $query->orderBy($orderByLawyer, $dirLawyer)->paginate(10, ['*'], 'lawyersPage')
                ->appends(['sort_lawyer' => $sortLawyer, 'dir_lawyer' => $dirLawyer]);
            $lawyersSimple   = Lawyer::orderBy('id', 'asc')->paginate(10, ['*'], 'lawyersSimplePage');
            $assistants = $assistantQuery
                ->orderBy($orderByAssistant, $dirAssistant)
                ->paginate(10, ['*'], 'assistantsPage')
                ->appends(['sort_assistant' => $sortAssistant, 'dir_assistant' => $dirAssistant]);
            $assistantsSimple = Assistant::with('lawyers')->orderBy('id', 'asc')->paginate(10, ['*'], 'assistantsSimplePage');
            $procesosSimple  = $procesosQuery->orderBy('id', 'asc')->paginate(10, ['*'], 'procesosSimplePage');

            $abogados = Lawyer::all();

            foreach ([$lawyers, $assistants, $lawyersSimple, $assistantsSimple, $procesosSimple] as $p) {
                $p->appends([
                    'search'   => $searchTerm,
                    'radicado' => $radicado
                ]);
            }

            if ($request->ajax()) {
                if ($request->get('section') === 'lawyers') { 
                    return response()->json([
                        'success' => true,
                        'html'    => view('profile.partials.lawyers-table', ['lawyers' => $lawyers])->render(),
                        'total'   => $lawyers->total()
                    ]);
                }
                if ($request->get('section') === 'assistants') {
                    return response()->json([
                        'success' => true,
                        'html'    => view('profile.partials.assistants-table', ['assistants' => $assistants])->render(),
                        'total'   => $assistants->total()
                    ]);
                }

                if ($request->has('radicado') || $request->has('procesosSimplePage')) {
                    return response()->json([
                        'success' => true,
                        'html'    => view('profile.partials.procesos-table-simple', [
                            'procesosSimple' => $procesosSimple
                        ])->render()
                    ]);
                }

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

            $totalLawyers    = Lawyer::count();
            $cases_count     = Proceso::count();
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