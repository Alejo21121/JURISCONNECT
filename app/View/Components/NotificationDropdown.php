<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NotificationDropdown extends Component
{
    public $notificaciones;

    public function __construct()
    {
        $this->notificaciones = auth()->user()->unreadNotifications;
    }

    public function render()
    {
        return view('components.notification-dropdown');
    }
}
