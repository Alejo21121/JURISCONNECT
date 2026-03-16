<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ProcesoProximoVencer extends Notification
{
    use Queueable;

    protected $proceso;
    protected $diasRestantes;
    public $id;

    public function __construct($proceso, $diasRestantes)
    {
        $this->proceso = $proceso;
        $this->diasRestantes = (int) $diasRestantes;
        $this->id = Str::uuid()->toString();
    }

    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        $dias = (int) $this->diasRestantes;

        $mensaje = match ($dias) {
            7 => "El proceso {$this->proceso->numero_radicado} vence en 7 días",
            3 => "El proceso {$this->proceso->numero_radicado} vence en 3 días",
            1 => "⚠️ El proceso {$this->proceso->numero_radicado} vence mañana",
            0 => "🔴 El proceso {$this->proceso->numero_radicado} vence HOY",
            -1 => "🚨 El proceso {$this->proceso->numero_radicado} ya venció",
            default => $dias < 0
                ? "🚨 El proceso {$this->proceso->numero_radicado} venció hace " . abs($dias) . " días"
                : "El proceso {$this->proceso->numero_radicado} vence en {$dias} días"
        };

        return [
            'titulo' => 'Proceso próximo a vencer',
            'mensaje' => $mensaje,
            'proceso_id' => $this->proceso->id,
            'radicado' => $this->proceso->numero_radicado,
            'dias_restantes' => $dias,
            'fecha_vencimiento' => $this->proceso->fecha_vencimiento,
            'tipo' => 'vencimiento',
        ];
    }
}
