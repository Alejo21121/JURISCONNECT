<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>JurisConnect SENA - Recuperar Contraseña</title>
    <link rel="stylesheet" href="{{ asset('/css/recuperar.css') }}">
</head>

<body>
    <!-- Fondo -->
    <div class="background-image">
        <img src="{{ asset('img/Login.jpg') }}" alt="Fondo">
    </div>

    {{-- -------------- Lógica Blade PRIMERO para definir variables -------------- --}}
    @php
        // error de 'email' si existe
        $emailError = $errors->first('email') ?? null;
        $showThrottle = false;
        $showNotRegistered = false;
        $throttleSeconds = null;

        if ($emailError) {
            // Normalizamos a minúsculas para comparaciones
            $lower = mb_strtolower($emailError);

            //Email no registrado
            // "No podemos encontrar un usuario con esa dirección de correo."
            if (
                preg_match(
                    '/no podemos encontrar|can\'t find|not find|no encontrado|no existe|no registrado|not registered|doesn\'t exist/i',
                    $lower,
                )
            ) {
                $showNotRegistered = true;
            }
            // Caso 2: mensajes que contienen la palabra "segundo(s)" o "second(s)" y un número
            elseif (preg_match('/(\d+)\s*(segundo|segundos|second|seconds)/i', $emailError, $m)) {
                $throttleSeconds = (int) $m[1];
                $showThrottle = true;
            }
            // Caso 3: mensajes con palabras clave comunes (incluye "wait", "retry", "retrying")
            elseif (preg_match('/wait|espera|inténtalo|demasiad|too many|throttl|esperar|retry|retrying/i', $lower)) {
                $showThrottle = true;
            }
        }

        // Laravel a veces pone el estado en session('status')
        $status = session('status') ?? null;
        if (!$showThrottle && !$showNotRegistered && $status) {
            $lowerStatus = mb_strtolower($status);
            if (preg_match('/throttl|espera|segundo|seconds|too many|wait|retry/i', $lowerStatus)) {
                $showThrottle = true;
                // Intentar extraer segundos si vienen en el status
                if (preg_match('/(\d+)\s*(segundo|segundos|second|seconds)/i', $status, $m2)) {
                    $throttleSeconds = (int) $m2[1];
                }
            }
        }
    @endphp

    <!-- Contenedor principal -->
    <div class="main-container">
        <!-- Izquierda: Logo -->
        <div class="left-section">
            <img class="logo-jurisconnect" src="{{ asset('img/LogoJ.png') }}" alt="JurisConnect">
        </div>

        <!-- Derecha: Formulario -->
        <div class="form-container">
            <h2 class="form-title">Recuperar Acceso</h2>
            <p class="form-description">Ingresa tu correo electrónico</p>

            <form method="POST" action="{{ route('password.email') }}" id="recoveryForm">
                @csrf
                <div class="form-group">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Correo Electrónico" class="form-input">
                    {{-- Solo mostramos el error debajo del input si NO es un error de throttle ni de no registrado --}}
                    @if ($errors->has('email') && !$showThrottle && !$showNotRegistered)
                        <div class="input-error">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div class="button-group">
                    <a href="{{ route('login') }}" class="btn btn-back">Volver</a>
                    <button type="submit" class="btn btn-next">Enviar</button>
                </div>

                <!-- Logo SENA -->
                <div class="sena-container">
                    <img src="{{ asset('img/Sena.png') }}" alt="Logo SENA" class="sena-logo">
                </div>
            </form>
        </div>
    </div>

    <!-- Alerta Éxito -->
    <div class="custom-alert-overlay" id="alertOverlay">
        <div class="custom-alert-box">
            <div class="custom-alert-icon-circle">✔</div>
            <h3 class="custom-alert-title">¡Correo Enviado!</h3>
            <p class="custom-alert-message">
                Hemos enviado las instrucciones de recuperación a tu correo electrónico.
                Por favor revisa tu bandeja de entrada y sigue las instrucciones.
            </p>
            <button class="custom-alert-btn" onclick="closeAlert()">Entendido</button>
        </div>
    </div>

    <!-- Alerta Error Throttle -->
    <div class="custom-alert-overlay" id="alertErrorOverlay">
        <div class="custom-alert-box">
            <div class="custom-alert-icon-circle error">✖</div>
            <h3 class="custom-alert-title">¡Espera un momento!</h3>
            <p class="custom-alert-message" id="alertErrorMessage">
                Debes esperar 30 segundos antes de solicitar un nuevo enlace. Inténtalo nuevamente en un momento.
            </p>
            <button class="custom-alert-btn" onclick="closeErrorAlert()">Entendido</button>
        </div>
    </div>

    <!-- Alerta Email No Registrado -->
    <div class="custom-alert-overlay" id="alertNotRegisteredOverlay">
        <div class="custom-alert-box">
            <div class="custom-alert-icon-circle warning">⚠</div>
            <h3 class="custom-alert-title">Correo No Registrado</h3>
            <p class="custom-alert-message">
                El correo electrónico ingresado no está registrado en nuestro sistema.
                Por favor verifica que hayas escrito correctamente tu correo o contacta al administrador.
            </p>
            <button class="custom-alert-btn" onclick="closeNotRegisteredAlert()">Entendido</button>
        </div>
    </div>

    {{-- Mostrar alerta de email no registrado --}}
    @if ($showNotRegistered)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotRegisteredAlert();
            });
        </script>
    @endif

    {{-- Mostrar alerta error si detectamos throttle --}}
    @if ($showThrottle)
        @php
            // Construimos un mensaje más claro si tenemos los segundos
            if ($throttleSeconds && $throttleSeconds > 0) {
                $throttleMsg = "Debes esperar {$throttleSeconds} segundos antes de solicitar un nuevo enlace. Inténtalo nuevamente en un momento.";
            } else {
                $throttleMsg =
                    'Debes esperar 30 segundos antes de solicitar un nuevo enlace. Inténtalo nuevamente en un momento.';
            }
        @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorAlert(@json($throttleMsg));
            });
        </script>
    @endif

    {{-- Mostrar alerta de éxito si Laravel puso session('status') indicando envío --}}
    @if (session('status') && !$showThrottle && !$showNotRegistered)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlert();
            });
        </script>
    @endif

    <script src="{{ asset('js/recuperar.js') }}"></script>

</body>

</html>
