<?php
// app/Http/Controllers/PagoController.php

namespace App\Http\Controllers;

use App\Models\Proceso;
use App\Models\Pago;
use App\Models\Lawyer;
use App\Models\Assistant;
use App\Models\PagoDocumento;
use Illuminate\Http\Request;
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
                ->with('pago')
                ->get();
        }
        // Verificar si es asistente
        elseif ($assistant = Assistant::where('user_id', $user->id)->first()) {
            $lawyerIds = $assistant->lawyers()->pluck('lawyers.id');
            $procesos = Proceso::whereIn('lawyer_id', $lawyerIds)
                ->with('pago')
                ->get();
        }
        // Usuario admin o sin rol específico
        else {
            $procesos = Proceso::with('pago')->get();
        }

        // Transformar datos para la vista
        $procesosData = $procesos->map(function ($proceso) {
            return [
                'id' => $proceso->id,
                'nombre' => "{$proceso->tipo_proceso} - {$proceso->demandante} - {$proceso->demandado}",
                'demandante' => $proceso->demandante,
                'radicado' => $proceso->numero_radicado,
                'requiere_pago' => $proceso->requiere_pago,
                'pago_realizado' => $proceso->pago !== null,
                'valor_estimado' => $proceso->valor_estimado,
                'valor_sentencia' => $proceso->pago?->valor_pagado,
                'forma_pago' => $proceso->pago?->forma_pago,
                'fecha_pago' => $proceso->pago?->fecha_pago,
                'observaciones' => $proceso->pago?->observaciones,
                'comprobante' => $proceso->pago?->documentos->first()?->ruta,
                'tipo_comprobante' => $proceso->pago?->documentos->first()?->tipo,
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

        // 🔒 Buscar proceso
        $proceso = Proceso::findOrFail($validated['proceso_id']);


        // 🚫 si ya está archivado, no permitir pago
        if ($proceso->estado === 'Archivado') {
            return response()->json([
                'success' => false,
                'message' => 'Este proceso ya está archivado y no puede modificarse'
            ], 403);
        }

        // Crear pago
        $pago = Pago::create([
            'proceso_id' => $validated['proceso_id'],
            'valor_pagado' => $validated['valor_pagado'],
            'forma_pago' => $validated['forma_pago'],
            'fecha_pago' => $validated['fecha_pago'],
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        // Guardar archivo
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

        // 🔥 CAMBIO DE ESTADO AUTOMÁTICO
        $proceso->update([
            'estado' => 'Archivado'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pago y comprobante registrados correctamente'
        ]);
    }
}
