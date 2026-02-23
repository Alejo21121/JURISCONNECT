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
        $this->diasRestantes = $diasRestantes;
        $this->id = Str::uuid()->toString();
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $mensaje = match ($this->diasRestantes) {
            7 => "El proceso {$this->proceso->numero_radicado} vence en 7 días",
            3 => "El proceso {$this->proceso->numero_radicado} vence en 3 días",
            1 => "⚠️ El proceso {$this->proceso->numero_radicado} vence mañana",
            0 => "🔴 El proceso {$this->proceso->numero_radicado} vence HOY",
            default => "El proceso {$this->proceso->numero_radicado} vence en {$this->diasRestantes} días"
        };

        return [
            'titulo' => 'Proceso próximo a vencer',
            'mensaje' => $mensaje,
            'proceso_id' => $this->proceso->id,
            'radicado' => $this->proceso->numero_radicado,
            'dias_restantes' => $this->diasRestantes,
            'fecha_vencimiento' => $this->proceso->fecha_vencimiento,
            'tipo' => 'vencimiento',
        ];
    }
}
