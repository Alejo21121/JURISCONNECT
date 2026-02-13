<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AsistenteAsignado extends Notification
{
    use Queueable;

    protected $abogado;

    public function __construct($abogado)
    {
        $this->abogado = $abogado;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'titulo' => 'Has sido asignado a un abogado',
            'mensaje' => 'Ahora trabajarás con el abogado ' . $this->abogado->nombre . ' ' . $this->abogado->apellido,
            'abogado_id' => $this->abogado->id,
            'abogado_nombre' => $this->abogado->nombre . ' ' . $this->abogado->apellido,
            'tipo' => 'asignacion'
        ];
    }
}