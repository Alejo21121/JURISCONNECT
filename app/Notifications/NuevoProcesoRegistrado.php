<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevoProcesoRegistrado extends Notification
{
    use Queueable;

    protected $proceso;
    protected $abogado;

    public function __construct($proceso, $abogado)
    {
        $this->proceso = $proceso;
        $this->abogado = $abogado;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'titulo' => 'Nuevo proceso registrado',
            'mensaje' => 'El abogado ' . $this->abogado . ' registró el proceso ' . $this->proceso->numero_radicado . ': ' . $this->proceso->demandante . ' vs ' . $this->proceso->demandado,
            'proceso_id' => $this->proceso->id,
            'proceso_nombre' => $this->proceso->numero_radicado,
            'radicado' => $this->proceso->numero_radicado,
            'abogado_nombre' => $this->abogado,
            'tipo' => 'proceso_nuevo'
        ];
    }
}
