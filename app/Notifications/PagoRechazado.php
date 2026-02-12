<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoRechazado extends Notification
{
    use Queueable;

    protected $pago;
    protected $motivo;

    public function __construct($pago, $motivo)
    {
        $this->pago = $pago;
        $this->motivo = $motivo;
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
            'titulo' => '❌ Pago rechazado',
            'mensaje' => "{$nombreAbogado} rechazó tu pago de " . number_format($this->pago->valor_pagado, 0, ',', '.') . " COP del proceso #{$proceso->numero_radicado}",
            'motivo' => $this->motivo,
            'proceso_id' => $this->pago->proceso_id,
            'pago_id' => $this->pago->id,
            'valor' => $this->pago->valor_pagado,
            'radicado' => $proceso->numero_radicado,
            'abogado' => $nombreAbogado,
        ];
    }
}