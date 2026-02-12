<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class PagoPendienteNotificacion extends Notification
{
    use Queueable;

    public $pago;
    private $nombreAsistente;

    public function __construct($pago)
    {
        $this->pago = $pago;

        // 👇 OBTENER EL NOMBRE DEL ASISTENTE AUTENTICADO AHORA MISMO
        $user = auth()->user();
        $assistant = \App\Models\Assistant::where('user_id', $user->id)->first();

        $this->nombreAsistente = $assistant
            ? $assistant->nombre . ' ' . $assistant->apellido
            : $user->name; // Fallback al nombre del usuario si no es asistente
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $proceso = $this->pago->proceso;

        return [
            'titulo' => 'Nuevo pago pendiente de aprobación',
            'mensaje' => "{$this->nombreAsistente} registró un pago de " . number_format($this->pago->valor_pagado, 0, ',', '.') . " COP para el proceso #{$proceso->numero_radicado}",
            'proceso_id' => $this->pago->proceso_id,
            'pago_id' => $this->pago->id,
            'valor' => $this->pago->valor_pagado,
            'radicado' => $proceso->numero_radicado,
            'asistente' => $this->nombreAsistente,
        ];
    }
}
