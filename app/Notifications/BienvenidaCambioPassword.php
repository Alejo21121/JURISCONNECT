<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BienvenidaCambioPassword extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'titulo' => '¡Bienvenido al sistema!',
            'mensaje' => 'Por seguridad, te recomendamos cambiar tu contraseña.',        // 👈 Asume que tienes esta ruta
            'tipo' => 'bienvenida'
        ];
    }
}