<?php
// app/Http/Controllers/PagoController.php

namespace App\Http\Controllers;

use App\Models\Proceso;
use App\Models\Pago;
use App\Models\Lawyer;
use App\Models\Assistant;
use App\Models\PagoDocumento;
use Illuminate\Http\Request;
use App\Models\HistorialEstadoProceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Proceso::with([
            'cuotas.pago.documentos'
        ]);

        $lawyer = Lawyer::where('user_id', $user->id)->first();

        if ($lawyer) {
            $query->where('lawyer_id', $lawyer->id);
        } elseif ($assistant = Assistant::where('user_id', $user->id)->first()) {
            $lawyerIds = $assistant->lawyers()->pluck('lawyers.id');
            $query->whereIn('lawyer_id', $lawyerIds);
        }

        $procesos = $query->get();

        // Transformar datos para la vista
        $procesosData = $procesos->map(function ($proceso) {
            // Calcular totales de cuotas
            $totalPagado = $proceso->cuotas->where('estado', 'Pagada')->sum('valor');
            $valorTotal = $proceso->valor_estimado ?? 0;
            $faltaPagar = max(0, $valorTotal - $totalPagado);
            $porcentaje = $valorTotal > 0 ? round(($totalPagado / $valorTotal) * 100, 2) : 0;

            return [
                'id' => $proceso->id,
                'nombre' => "{$proceso->tipo_proceso} - {$proceso->demandante} - {$proceso->demandado}",
                'demandante' => $proceso->demandante,
                'radicado' => $proceso->numero_radicado,
                'requiere_pago' => $proceso->requiere_pago,
                'pago_realizado' => $proceso->pago !== null,

                // Datos de cuotas
                'valor_estimado' => $proceso->valor_estimado,
                'total_pagado' => $totalPagado,
                'falta_pagar' => $faltaPagar,
                'porcentaje' => $porcentaje,

                // Historial de pagos (cuotas)
                'cuotas' => $proceso->cuotas->map(function ($cuota) {
                    return [
                        'id' => $cuota->id,
                        'pago_id' => $cuota->pago?->id,
                        'valor_pagado' => $cuota->valor,
                        'fecha_pago' => $cuota->fecha_pago,
                        'estado' => $cuota->estado,
                        'pago_estado' => $cuota->pago?->estado,
                        'motivo_rechazo' => $cuota->pago?->motivo_rechazo,
                        'observaciones' => $cuota->pago?->observaciones,
                        'comprobante' => $cuota->pago?->documentos->first()?->ruta,
                    ];
                }),
            ];
        });

        // 👇 AGREGAR ESTO
        $esAbogado = $lawyer !== null; // true si es abogado, false si es asistente

        return view('pagos', compact('procesosData', 'esAbogado'));
    }

    public function store(Request $request)
    {
        // 🔑 Limpiar el valor
        if ($request->valor_pagado) {
            $request->merge([
                'valor_pagado' => preg_replace('/\D/', '', $request->valor_pagado)
            ]);
        }

        $validated = $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
            'valor_pagado' => 'required|numeric|min:0',
            'forma_pago' => 'required|in:Efectivo,Transferencia,Consignación,Tarjeta,Otro',
            'fecha_pago' => 'required|date',
            'observaciones' => 'nullable|string|max:1000',
            'comprobante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $proceso = Proceso::with('cuotas')->findOrFail($validated['proceso_id']);

        if ($proceso->estado === 'Archivado') {
            return response()->json([
                'success' => false,
                'message' => 'Este proceso ya está archivado y no puede modificarse'
            ], 403);
        }

        // 🔥 NUEVA VALIDACIÓN: Sumar pagos APROBADOS + PENDIENTES
        $totalPagado = $proceso->cuotas
            ->where('estado', 'Pagada')
            ->sum('valor');

        $totalPendiente = $proceso->cuotas()
            ->whereHas('pago', function ($q) {
                $q->where('estado', 'Pendiente');
            })
            ->sum('valor');

        $totalComprometido = $totalPagado + $totalPendiente;
        $faltaPagar = $proceso->valor_estimado - $totalComprometido;

        if ($validated['valor_pagado'] > $faltaPagar) {
            return response()->json([
                'success' => false,
                'message' => "El valor excede el monto pendiente. Disponible para pago: " . number_format($faltaPagar, 0, ',', '.') . " COP (incluyendo pagos en revisión)"
            ], 422);
        }

        // 🔥 VERIFICAR SI ES ABOGADO
        $user = Auth::user();
        $lawyer = Lawyer::where('user_id', $user->id)->first();
        $esAbogado = $lawyer !== null;

        $estadoPago = $esAbogado ? 'Aprobado' : 'Pendiente';
        $estadoCuota = $esAbogado ? 'Pagada' : 'Pendiente';

        // Crear pago
        $pago = Pago::create([
            'proceso_id' => $validated['proceso_id'],
            'valor_pagado' => $validated['valor_pagado'],
            'forma_pago' => $validated['forma_pago'],
            'fecha_pago' => $validated['fecha_pago'],
            'observaciones' => $validated['observaciones'] ?? null,
            'estado' => $estadoPago,
            'validado_por' => $esAbogado ? $user->id : null,
            'fecha_validacion' => $esAbogado ? now() : null,
        ]);

        // Guardar comprobante
        if ($request->hasFile('comprobante')) {
            $file = $request->file('comprobante');
            $ruta = $file->store('pagos/comprobantes', 'public');

            PagoDocumento::create([
                'pago_id' => $pago->id,
                'nombre' => $file->getClientOriginalName(),
                'ruta' => $ruta,
                'tipo' => $file->extension(),
                'tamano' => $file->getSize(),
            ]);
        }

        // Crear cuota
        $cuota = \App\Models\Cuota::create([
            'proceso_id' => $proceso->id,
            'pago_id' => $pago->id,
            'numero_cuota' => $proceso->cuotas->count() + 1,
            'valor' => $validated['valor_pagado'],
            'fecha_vencimiento' => $validated['fecha_pago'],
            'estado' => $estadoCuota,
            'fecha_pago' => $validated['fecha_pago'],
        ]);

        // 🔥 VERIFICAR SI SE COMPLETÓ EL TOTAL (solo si es abogado)
        if ($esAbogado) {
            $proceso->load('cuotas');

            $nuevoTotalPagado = $proceso->cuotas
                ->where('estado', 'Pagada')
                ->sum('valor');

            if ($nuevoTotalPagado >= $proceso->valor_estimado) {
                $proceso->update(['estado' => 'Archivado']);

                HistorialEstadoProceso::create([
                    'proceso_id' => $proceso->id,
                    'estado' => 'Archivado',
                    'observacion' => 'Pago completado automáticamente - Proceso archivado',
                    'user_id' => $user->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado correctamente'
        ]);
    }

    public function rechazar(Request $request, Pago $pago)
    {
        $request->validate([
            'motivo' => 'required|string|min:5'
        ]);

        $pago->update([
            'estado' => 'Rechazado',
            'motivo_rechazo' => $request->motivo,
            'validado_por' => Auth::id(),
            'fecha_validacion' => now(),
        ]);

        if ($pago->cuota) {
            $pago->cuota->update([
                'estado' => 'Pendiente'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago rechazado correctamente'
        ]);
    }

    public function aprobar(Pago $pago)
    {
        $pago->update([
            'estado' => 'Aprobado',
            'validado_por' => Auth::id(),
            'fecha_validacion' => now(),
        ]);

        if ($pago->cuota) {
            $pago->cuota->update([
                'estado' => 'Pagada'
            ]);
        }

        $proceso = $pago->proceso()->with('cuotas')->first();

        $totalPagado = $proceso->cuotas
            ->where('estado', 'Pagada')
            ->sum('valor');

        if ($totalPagado >= $proceso->valor_estimado) {

            $proceso->update([
                'estado' => 'Archivado'
            ]);

            HistorialEstadoProceso::create([
                'proceso_id' => $proceso->id,
                'estado' => 'Archivado',
                'observacion' => 'Pago completado - Proceso archivado',
                'user_id' => Auth::id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago aprobado correctamente'
        ]);
    }
}
