<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class NewPasswordController extends Controller
{
    /**
     * Muestra la vista de restablecer contraseña.
     */
    public function create(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Procesa el restablecimiento de la contraseña.
     */
    public function store(Request $request)
    {
        // Validación estricta
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed', 
                PasswordRule::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.letters' => 'Debe incluir al menos una letra.',
            'password.mixed_case' => 'Debe incluir mayúsculas y minúsculas.',
            'password.numbers' => 'Debe incluir al menos un número.',
            'password.symbols' => 'Debe incluir al menos un carácter especial.',
        ]);

        // Intentar restablecer contraseña
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60), 
                ])->save();

                event(new PasswordReset($user)); 
            }
        );

        return match ($status) {
            Password::PASSWORD_RESET =>
            redirect()->route('login')
                ->with('status', 'Tu contraseña ha sido restablecida correctamente.'),

            Password::INVALID_TOKEN =>
            back()->withErrors([
                'email' => __('passwords.token'),
            ]),

            default =>
            back()->withErrors([
                'email' => __('passwords.user'),
            ]),
        };
    }
}
