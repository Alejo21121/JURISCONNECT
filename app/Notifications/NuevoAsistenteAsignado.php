<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevoAsistenteAsignado extends Notification
{
    use Queueable;

    protected $asistente;

    public function __construct($asistente)
    {
        $this->asistente = $asistente;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'titulo' => 'Nuevo asistente asignado',
            'mensaje' => 'El asistente ' . $this->asistente->nombre . ' ' . $this->asistente->apellido . ' ahora trabaja contigo',
            'asistente_id' => $this->asistente->id,
            'asistente_nombre' => $this->asistente->nombre . ' ' . $this->asistente->apellido,
            'tipo' => 'asignacion'
        ];
    }
}