<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        Log::info('Request completo:', $request->all());
        Log::info('Tiene archivo:', ['tiene' => $request->hasFile('profile_photo')]);
        Log::info('Archivo:', ['file' => $request->file('profile_photo')]);

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();

        if (!empty($user->foto_perfil)) {
            Storage::disk('public')->delete($user->foto_perfil);
        }

        $path = $request->file('profile_photo')->store('profile_photos', 'public');

        $user->foto_perfil = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'url' => Storage::url($path),
            'message' => 'Foto actualizada correctamente'
        ]);
    }

    public function show()
    {
        $user = Auth::user();

        // Obtener datos adicionales según el rol
        $additionalData = null;

        if ($user->role_id == 2 || $user->role_id == 4) {
            // Abogado
            $additionalData = $user->lawyer;
        } elseif ($user->role_id == 3) {
            // Asistente
            $additionalData = $user->assistant;
        }

        return view('profile.show', compact('user', 'additionalData'));
    }

    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Actualizar contraseña
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        switch ($user->role_id) {
            case 1: // Admin
                return redirect()->route('dashboard')
                    ->with('password_updated', true);

            case 2: // Abogado
                return redirect()->route('dashboard.abogado')
                    ->with('password_updated', true);

            case 3: // Asistente
                return redirect()->route('dashboard.asistente')
                    ->with('password_updated', true);

            default:
                return back()->with('password_updated', true);
        }
    }
}
