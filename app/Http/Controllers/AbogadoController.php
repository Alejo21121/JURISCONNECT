<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConceptoJuridico;
use Illuminate\Support\Facades\Auth;
use App\Models\Proceso;
use Illuminate\Routing\Controller;

class AbogadoController extends Controller
{
    // ===============================
    // MÉTODOS PRINCIPALES DE DASHBOARD
    // ===============================

    /**
     * Dashboard principal del abogado
     */
    public function index()
    {

        return view('dashboard-abogado',);
    }

    /**
     * Mostrar procesos asignados al abogado
     */
    public function misProcesos()
    {
        $procesos = ConceptoJuridico::where('abogado_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('legal_processes.conceptos', compact('procesos'));
    }

    // ===============================
    // MÉTODOS DE GESTIÓN DE CONCEPTOS
    // ===============================

    /**
     * Mostrar lista de procesos o concepto específico
     */
    public function crearConcepto($id = null)
    {
        // Si no se pasa ID, mostrar lista de procesos pendientes
        if (!$id) {
            return $this->mostrarProcesosPendientes();
        }

        // Si se pasa ID, mostrar el formulario para ese proceso específico
        return $this->mostrarFormularioConcepto($id);
    }

    /**
     * Guardar el concepto jurídico
     */
    public function guardarConcepto(Request $request, $id)
    {
        $this->validarConcepto($request);

        try {
            $proceso = ConceptoJuridico::findOrFail($id);

            $this->verificarAccesoProceso($proceso);
            $this->verificarEstadoPendiente($proceso);

            // Actualizar el proceso con el concepto
            $proceso->update([
                'estado' => 'en curso',
                'updated_at' => now()
            ]);

            return redirect()->route('abogado.crear-concepto')
                ->with('success', 'Concepto jurídico guardado exitosamente. El proceso ha sido marcado como "en curso".');
        } catch (\Exception $e) {
            return redirect()
                ->route('conceptos.create', $id)
                ->with('success', 'Concepto jurídico creado correctamente.');
        }
    }

    /**
     * Actualizar concepto jurídico
     */
    public function updateConcepto(Request $request, $procesoId, $conceptoId)
    {
        $this->validarConcepto($request);

        try {
            $proceso = ConceptoJuridico::findOrFail($procesoId);

            $this->verificarAccesoProceso($proceso);

            // Actualizar concepto y recomendaciones
            $proceso->concepto = $request->concepto;
            $proceso->recomendaciones = $request->recomendaciones;
            $proceso->updated_at = now();
            $proceso->save();

            return redirect()->route('abogado.crear-concepto')
                ->with('success', 'Concepto jurídico actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el concepto: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ===============================
    // MÉTODOS DE GESTIÓN DE PROCESOS
    // ===============================

    /**
     * Mostrar lista de procesos pendientes de concepto
     */
    public function procesosPendientes()
    {
        $procesos = $this->obtenerProcesosPendientes();
        return view('abogado.procesos-pendientes', compact('procesos'));
    }

    /**
     * Ver detalles de un proceso específico
     */
    public function verProceso($id)
    {
        try {
            $proceso = ConceptoJuridico::findOrFail($id);

            $this->verificarAccesoProceso($proceso);

            return view('abogado.detalle-proceso', compact('proceso'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Proceso no encontrado');
        }
    }

    /**
     * Finalizar proceso (cambiar estado a finalizado)
     */
    public function finalizarProceso($id)
    {
        try {
            $proceso = ConceptoJuridico::findOrFail($id);

            $this->verificarAccesoProceso($proceso);
            $this->verificarEstadoEnCurso($proceso);

            $proceso->update(['estado' => 'finalizado']);

            return redirect()->route('abogado.mis-procesos')
                ->with('success', 'Proceso finalizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al finalizar el proceso');
        }
    }

    // ===============================
    // MÉTODOS PRIVADOS DE APOYO
    // ===============================

    /**
     * Obtener estadísticas del abogado
     */
    private function getEstadisticas()
    {
        $abogadoId = Auth::id();

        $totalProcesos = ConceptoJuridico::where('abogado_id', $abogadoId)->count();
        $procesosPendientes = ConceptoJuridico::where('abogado_id', $abogadoId)
            ->where('estado', 'pendiente')
            ->count();
        $procesosFinalizados = ConceptoJuridico::where('abogado_id', $abogadoId)
            ->where('estado', 'finalizado')
            ->count();
        $procesosEnCurso = ConceptoJuridico::where('abogado_id', $abogadoId)
            ->where('estado', 'en curso')
            ->count();

        return compact('totalProcesos', 'procesosPendientes', 'procesosFinalizados', 'procesosEnCurso');
    }

    /**
     * Obtener procesos pendientes del abogado
     */
    private function obtenerProcesosPendientes()
    {
        return ConceptoJuridico::where('abogado_id', Auth::id())
            ->where('estado', 'pendiente')
            ->orderBy('fecha_radicacion', 'asc')
            ->get();
    }

    /**
     * Mostrar lista de procesos pendientes
     */
    private function mostrarProcesosPendientes()
    {
        $procesos = $this->obtenerProcesosPendientes();
        return view('legal_processes.editConceptos', compact('procesos'));
    }

    /**
     * Mostrar formulario para concepto específico
     */
    public function mostrarFormularioConcepto($id)
    {
        try {
            $proceso = Proceso::findOrFail($id);

            return view('legal_processes.createConceptos', compact('proceso'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Proceso no encontrado');
        }
    }

    /**
     * Validar datos del concepto
     */
    private function validarConcepto(Request $request)
    {
        $request->validate([
            'concepto' => 'required|string|min:50',
            'recomendaciones' => 'nullable|string|max:2000',
        ], [
            'concepto.required' => 'El concepto jurídico es obligatorio',
            'concepto.min' => 'El concepto jurídico debe tener al menos 50 caracteres',
            'recomendaciones.max' => 'Las recomendaciones no pueden exceder 2000 caracteres'
        ]);
    }

    /**
     * Verificar que el abogado tenga acceso al proceso
     */
    private function verificarAccesoProceso($proceso)
    {
        if ($proceso->abogado_id !== Auth::id()) {
            abort(403, 'No tienes acceso a este proceso');
        }
    }

    /**
     * Verificar que el proceso esté en estado pendiente
     */
    private function verificarEstadoPendiente($proceso)
    {
        if ($proceso->estado !== 'pendiente') {
            abort(400, 'Este proceso ya no está pendiente de concepto');
        }
    }

    /**
     * Verificar que el proceso esté en estado "en curso"
     */
    private function verificarEstadoEnCurso($proceso)
    {
        if ($proceso->estado !== 'en curso') {
            abort(400, 'Solo se pueden finalizar procesos que estén en curso');
        }
    }

    // ===============================
    // MÉTODO LEGACY (MANTENER POR COMPATIBILIDAD)
    // ===============================

    /**
     * @deprecated Use getEstadisticas() instead
     */
    public function estadisticas()
    {
        return $this->getEstadisticas();
    }
}
