<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proceso;
use App\Notifications\ProcesoProximoVencer;
use Carbon\Carbon;

class VerificarProcesosVencimiento extends Command
{
    protected $signature = 'procesos:verificar-vencimiento';
    protected $description = 'Verifica procesos próximos a vencer y notifica';

    public function handle()
    {
        $hoy = Carbon::now()->startOfDay();

        // Buscar procesos activos
        $procesos = Proceso::whereIn('estado', [
            'Pendiente',
            'Radicado',
            'Admisión',
            'Traslado',
            'Audiencia',
            'Fallo favorable',
            'Fallo desfavorable',
            'Apelación',
            'Ejecutoria',
            'Pago en trámite',
            'Conciliado',
            'Reabierto'
        ])
            ->whereNotNull('fecha_vencimiento')
            ->get();

        $notificacionesEnviadas = 0;

        foreach ($procesos as $proceso) {
            $fechaVencimiento = Carbon::parse($proceso->fecha_vencimiento)->startOfDay();
            $diasRestantes = $hoy->diffInDays($fechaVencimiento, false);

            // Notificar en 7, 3, 1, 0 días y vencidos
            if (in_array($diasRestantes, [7, 3, 1, 0, -1, -3, -7])) {

                if ($proceso->lawyer && $proceso->lawyer->user) {
                    $proceso->lawyer->user->notify(
                        new ProcesoProximoVencer($proceso, $diasRestantes)
                    );
                    $notificacionesEnviadas++;
                }

                if ($proceso->lawyer && $proceso->lawyer->assistants) {
                    foreach ($proceso->lawyer->assistants as $assistant) {
                        if ($assistant->user) {
                            $assistant->user->notify(
                                new ProcesoProximoVencer($proceso, $diasRestantes)
                            );
                            $notificacionesEnviadas++;
                        }
                    }
                }
            }
        }

        $this->info("✅ Verificación completada. {$notificacionesEnviadas} notificaciones enviadas.");
    }
}
