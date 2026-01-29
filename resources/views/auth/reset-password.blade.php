<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">
    <title>JurisConnect SENA - Restablecer Contraseña</title>
    <link rel="stylesheet" href="{{ asset('/css/register.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Fondo -->
    <div class="background-image">
        <img src="{{ asset('img/Login.jpg') }}" alt="Fondo de Pantalla" class="background-image">
    </div>

    <!-- Izquierda: logo -->
    <div class="branding">
        <img src="{{ asset('img/LogoJ.png') }}" alt="JurisConnect">
    </div>

    <!-- Derecha: formulario -->
    <div class="login-box">
        <h2>Actualizar Contraseña</h2>

        <!-- Contenedor de alertas -->
        <div id="alert-container"></div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email -->
            <label for="email">Correo Electrónico</label>
            <input type="email" value="{{ old('email', request('email')) }}" readonly>
            <input type="hidden" name="email" value="{{ old('email', request('email')) }}">

            <!-- Password -->
            <label for="password">Nueva Contraseña</label>
            <div class="password-wrapper">
                <input id="password" type="password" name="password" required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <!-- Ojo cerrado -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeClosed-password" viewBox="0 0 24 24" fill="none"
                        stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.12 10.12 0 0 1 12 20c-7 0-11-8-11-8
                                a19.44 19.44 0 0 1 4.24-5.94M9.9 4.24A9.77 9.77 0 0 1 12 4
                                c7 0 11 8 11 8a19.44 19.44 0 0 1-4.24 5.94M1 1l22 22" />
                    </svg>
                    <!-- Ojo abierto -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeOpen-password" viewBox="0 0 24 24" fill="none"
                        stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="display:none;">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </span>
            </div>

            <!-- Requisitos de contraseña -->
            <div class="password-requirements">
                <h4>La contraseña debe contener:</h4>
                <div class="requirement" id="req-length">
                    <span class="requirement-icon">○</span>
                    <span>Mínimo 8 caracteres</span>
                </div>
                <div class="requirement" id="req-uppercase">
                    <span class="requirement-icon">○</span>
                    <span>Al menos una letra mayúscula (A-Z)</span>
                </div>
                <div class="requirement" id="req-lowercase">
                    <span class="requirement-icon">○</span>
                    <span>Al menos una letra minúscula (a-z)</span>
                </div>
                <div class="requirement" id="req-number">
                    <span class="requirement-icon">○</span>
                    <span>Al menos un número (0-9)</span>
                </div>
                <div class="requirement" id="req-special">
                    <span class="requirement-icon">○</span>
                    <span>Al menos un carácter especial (!@#$%^&*)</span>
                </div>
            </div>

            <!-- Confirm Password -->
            <label for="password_confirmation">Confirmar Contraseña</label>
            <div class="password-wrapper">
                <input id="password_confirmation" type="password" name="password_confirmation" required>
                <span class="toggle-password" onclick="togglePassword('password_confirmation')">
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeClosed-password_confirmation" viewBox="0 0 24 24"
                        fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.12 10.12 0 0 1 12 20c-7 0-11-8-11-8
                                a19.44 19.44 0 0 1 4.24-5.94M9.9 4.24A9.77 9.77 0 0 1 12 4
                                c7 0 11 8 11 8a19.44 19.44 0 0 1-4.24 5.94M1 1l22 22" />
                    </svg>
                    <!-- Ojo abierto -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeOpen-password_confirmation" viewBox="0 0 24 24"
                        fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="display:none;">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </span>
            </div>
            @error('password_confirmation')
                <span class="error-message">{{ $message }}</span>
            @enderror

            <!-- Botones -->
            <div class="button-group">
                <button type="button" class="btn btn-back" onclick="window.location='{{ url('/') }}'">
                    Cancelar
                </button>
                <button class="btn" type="submit">Guardar</button>
            </div>
        </form>
        <img src="{{ asset('img/Sena.png') }}" alt="Logo SENA" class="sena-logo">
    </div>
    <script src="{{ asset('/js/reset-password.js') }}"></script>
</body>

@if ($errors->has('email') && $errors->first('email') === __('passwords.token'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'error',
                title: 'Enlace expirado',
                text: 'El enlace de restablecimiento ya no es válido. Por favor solicita uno nuevo.',
                confirmButtonText: 'Solicitar nuevo enlace'
            }).then(() => {
                window.location.href = "/forgot-password";
            });
        });
    </script>
@endif

</html>
