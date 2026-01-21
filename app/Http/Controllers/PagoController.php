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

        // Verificar si es abogado
        $lawyer = Lawyer::where('user_id', $user->id)->first();
        if ($lawyer) {
            $procesos = Proceso::where('lawyer_id', $lawyer->id)
                ->with(['pago.documentos', 'cuotas'])
                ->get();
        }
        // Verificar si es asistente
        elseif ($assistant = Assistant::where('user_id', $user->id)->first()) {
            $lawyerIds = $assistant->lawyers()->pluck('lawyers.id');
            $procesos = Proceso::whereIn('lawyer_id', $lawyerIds)
                ->with(['pago.documentos', 'cuotas'])
                ->get();
        }
        // Usuario admin o sin rol específico
        else {
            $procesos = Proceso::with(['pago.documentos', 'cuotas'])->get();
        }

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
                        'valor_pagado' => $cuota->valor,
                        'fecha_pago' => $cuota->fecha_pago,
                        'estado' => $cuota->estado,
                        'observaciones' => $cuota->pago?->observaciones,
                        'comprobante' => $cuota->pago?->documentos->first()?->ruta,
                    ];
                }),
            ];
        });

        return view('pagos', compact('procesosData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
            'valor_pagado' => 'required|numeric|min:0',
            'forma_pago' => 'required|in:Efectivo,Transferencia,Consignación,Tarjeta,Otro',
            'fecha_pago' => 'required|date',
            'observaciones' => 'nullable|string|max:1000',
            'comprobante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Buscar proceso
        $proceso = Proceso::with('cuotas')->findOrFail($validated['proceso_id']);

        // si ya está archivado, no permitir pago
        if ($proceso->estado === 'Archivado') {
            return response()->json([
                'success' => false,
                'message' => 'Este proceso ya está archivado y no puede modificarse'
            ], 403);
        }

        // Verificar que no exceda el valor estimado
        $totalPagado = $proceso->cuotas->where('estado', 'Pagada')->sum('valor');
        $faltaPagar = $proceso->valor_estimado - $totalPagado;

        if ($validated['valor_pagado'] > $faltaPagar) {
            return response()->json([
                'success' => false,
                'message' => 'El valor excede el monto pendiente de pago'
            ], 422);
        }

        // Crear el registro de pago
        $pago = Pago::create([
            'proceso_id' => $validated['proceso_id'],
            'valor_pagado' => $validated['valor_pagado'],
            'forma_pago' => $validated['forma_pago'],
            'fecha_pago' => $validated['fecha_pago'],
            'observaciones' => $validated['observaciones'] ?? null,
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

        // Crear la cuota asociada
        $cuota = \App\Models\Cuota::create([
            'proceso_id' => $proceso->id,
            'pago_id' => $pago->id,
            'numero_cuota' => $proceso->cuotas->count() + 1,
            'valor' => $validated['valor_pagado'],
            'fecha_vencimiento' => $validated['fecha_pago'],
            'estado' => 'Pagada',
            'fecha_pago' => $validated['fecha_pago'],
        ]);

        // Verificar si se completó el pago total
        $nuevoTotalPagado = $totalPagado + $validated['valor_pagado'];

        if ($nuevoTotalPagado >= $proceso->valor_estimado) {
            // Pago completado -> Archivar
            $proceso->estado = 'Archivado';
            $proceso->save();

            HistorialEstadoProceso::create([
                'proceso_id' => $proceso->id,
                'estado' => 'Archivado',
                'observacion' => 'Pago completado - Proceso archivado',
                'user_id' => Auth::id(),
            ]);

            $mensaje = 'Pago registrado. El proceso ha sido ARCHIVADO porque se completó el pago total.';
        } else {
            // Pago parcial -> Cambiar a "Pago en trámite"
            if ($proceso->estado !== 'Pago en trámite') {
                $proceso->estado = 'Pago en trámite';
                $proceso->save();

                HistorialEstadoProceso::create([
                    'proceso_id' => $proceso->id,
                    'estado' => 'Pago en trámite',
                    'observacion' => 'Pago parcial registrado',
                    'user_id' => Auth::id(),
                ]);
            }

            $mensaje = 'Pago parcial registrado correctamente';
        }

        return response()->json([
            'success' => true,
            'message' => $mensaje
        ]);
    }
}
