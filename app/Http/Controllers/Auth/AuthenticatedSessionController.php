<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar la vista de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Manejar una solicitud de autenticación entrante.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        //  NOTIFICACIÓN DE BIENVENIDA - SOLO SI NO EXISTE
        if (!$user->password_changed) {
            // Verificar si ya tiene una notificación de bienvenida (leída o no)
            $tieneNotificacion = $user->notifications()
                ->where('type', 'App\\Notifications\\BienvenidaCambioPassword')
                ->exists();

            // Solo crear si no existe
            if (!$tieneNotificacion) {
                $user->notify(new \App\Notifications\BienvenidaCambioPassword());
            }
        }

        // Redirección basada en el rol (por ID)
        switch ($user->role_id) {
            case 1: // Administrador
                return redirect()->route('dashboard');
            case 2: // Abogado
                return redirect()->route('dashboard.abogado');
            case 3: // Asistente Jurídico
                return redirect()->route('dashboard.asistente');
            default:
                return redirect()->route('dashboard');
        }
    }

    /** 
     * Cerrar sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
