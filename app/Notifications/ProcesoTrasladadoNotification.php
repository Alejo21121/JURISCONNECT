<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProcesoTrasladadoNotification extends Notification
{
    use Queueable;

    public $proceso;
    public $nombreAbogado;

    public function __construct($proceso, $nombreAbogado)
    {
        $this->proceso = $proceso;
        $this->nombreAbogado = $nombreAbogado;
    }

    // ESTE MÉTODO ES OBLIGATORIO
    public function via($notifiable)
    {
        return ['database'];
        // Si quisieras email también:
        // return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipo' => 'traslado',
            'titulo' => 'Nuevo proceso asignado',
            'mensaje' => 'El abogado ' . $this->nombreAbogado .
                ' le trasladó el proceso ' . $this->proceso->numero_radicado,
            'proceso_id' => $this->proceso->id,
            'url' => route('mis.procesos'),
        ];
    }
}
