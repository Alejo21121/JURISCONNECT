<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PagoAprobado extends Notification
{
    use Queueable;

    protected $pago;

    public function __construct($pago)
    {
        $this->pago = $pago;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        // Obtener información del proceso y abogado
        $proceso = $this->pago->proceso;
        $abogado = $proceso->lawyer?->user;

        $nombreAbogado = $abogado
            ? $abogado->name
            : 'El abogado';

        return [
            'titulo' => '✅ Pago aprobado',
            'mensaje' => "{$nombreAbogado} aprobó tu pago de " . number_format($this->pago->valor_pagado, 0, ',', '.') . " COP del proceso #{$proceso->numero_radicado}",
            'proceso_id' => $this->pago->proceso_id,
            'pago_id' => $this->pago->id,
            'valor' => $this->pago->valor_pagado,
            'radicado' => $proceso->numero_radicado,
            'abogado' => $nombreAbogado,
        ];
    }
}
