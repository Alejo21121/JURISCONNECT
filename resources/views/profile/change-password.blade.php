<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- 👈 AGREGAR --}}
    @vite(['resources/js/dash.js'])
    <title>Cambiar Contraseña</title>
</head>

<body>
    <div class="dashboard-wrapper">
        <div class="overlay" id="overlay"></div>

        <x-sidebar />

        <main class="main-content" id="mainContent">

            {{-- Header --}}
            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <h1>Cambiar Contraseña</h1>
                </div>
                <div class="header-right">
                    <x-notification-dropdown />
                    <a href="{{ route('dashboard.abogado') }}">
                        <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
                    </a>
                </div>
            </header>

            {{-- Contenido --}}
            <div class="profile-container">
                <div class="profile-card">
                    <div class="profile-info">

                        <h3><i class="fas fa-lock"></i> Cambiar Contraseña</h3>

                        <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm">
                            @csrf
                            @method('PUT')

                            <div class="info-grid">

                                {{-- Contraseña Actual --}}
                                <div class="info-item">
                                    <label><i class="fas fa-lock"></i> Contraseña Actual *</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="current_password" id="current_password" required>
                                        <span class="toggle-password" onclick="togglePwd('current_password', this)">
                                            <svg id="eye-current_password" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a19.44 19.44 0 014.24-5.94M9.9 4.24A9.77 9.77 0 0112 4c7 0 11 8 11 8a19.44 19.44 0 01-2.4 3.38M1 1l22 22" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('current_password')
                                        <span class="error-text"><i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Nueva Contraseña --}}
                                <div class="info-item">
                                    <label><i class="fas fa-key"></i> Nueva Contraseña *</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password" id="new_password" required>
                                        <span class="toggle-password" onclick="togglePwd('new_password', this)">
                                            <svg id="eye-new_password" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a19.44 19.44 0 014.24-5.94M9.9 4.24A9.77 9.77 0 0112 4c7 0 11 8 11 8a19.44 19.44 0 01-2.4 3.38M1 1l22 22" />
                                            </svg>
                                        </span>
                                    </div>
                                    {{-- Barra de fortaleza --}}
                                    <div class="strength-bar-wrapper">
                                        <span class="strength-label" id="strengthLabel"></span>
                                    </div>
                                    @error('password')
                                        <span class="error-text"><i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Confirmar Contraseña --}}
                                <div class="info-item">
                                    <label><i class="fas fa-key"></i> Confirmar Nueva Contraseña *</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password_confirmation" id="confirm_password"
                                            required>
                                        <span class="toggle-password" onclick="togglePwd('confirm_password', this)">
                                            <svg id="eye-confirm_password" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a19.44 19.44 0 014.24-5.94M9.9 4.24A9.77 9.77 0 0112 4c7 0 11 8 11 8a19.44 19.44 0 01-2.4 3.38M1 1l22 22" />
                                            </svg>
                                        </span>
                                    </div>
                                    <span class="error-text" id="matchError" style="display:none;">
                                        <i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden.
                                    </span>
                                </div>

                                {{-- Requisitos --}}
                                <div class="info-item full-width">
                                    <div class="password-requirements">
                                        <h4><i class="fas fa-shield-alt"></i> La contraseña debe contener:</h4>
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
                                </div>

                            </div>

                            <div class="profile-actions">
                                <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Guardar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>

    @if (session('password_updated'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Contraseña Actualizada!',
                html: '<p>Tu contraseña ha sido cambiada correctamente.</p><p style="color: #16a34a; font-weight: 600;">Debes volver a iniciar sesión con tu nueva contraseña.</p>',
                confirmButtonText: 'Aceptar e Iniciar Sesión',
                confirmButtonColor: '#16a34a',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear y enviar formulario de logout
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('logout') }}";

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = "{{ csrf_token() }}";

                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showCustomAlert('error', 'Error', '{{ session('error') }}');
            });
        </script>
    @endif

    <script>
        /* ---- Toggle visibilidad contraseña ---- */
        function togglePwd(inputId, btn) {
            const input = document.getElementById(inputId);
            const svg = btn.querySelector('svg');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            svg.innerHTML = isHidden ?
                `<path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>` :
                `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a19.44 19.44 0 014.24-5.94M9.9 4.24A9.77 9.77 0 0112 4c7 0 11 8 11 8a19.44 19.44 0 01-2.4 3.38M1 1l22 22"/>`;
        }

        /* ---- Validación requisitos ---- */
        const pwdInput = document.getElementById('new_password');

        if (pwdInput) { // 👈 VERIFICAR QUE EXISTE
            const checks = {
                'req-length': v => v.length >= 8,
                'req-uppercase': v => /[A-Z]/.test(v),
                'req-lowercase': v => /[a-z]/.test(v),
                'req-number': v => /[0-9]/.test(v),
                'req-special': v => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(v),
            };

            const strengthLabel = document.getElementById('strengthLabel');
            const strengthColors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#16a34a'];
            const strengthTexts = ['', 'Muy débil', 'Débil', 'Regular', 'Fuerte', 'Muy fuerte'];

            pwdInput.addEventListener('input', function() {
                const val = this.value;
                let passed = 0;

                Object.entries(checks).forEach(([id, fn]) => {
                    const el = document.getElementById(id);
                    if (!el) return;

                    const icon = el.querySelector('.requirement-icon');
                    if (fn(val)) {
                        el.classList.add('valid');
                        if (icon) icon.textContent = '✓';
                        passed++;
                    } else {
                        el.classList.remove('valid');
                        if (icon) icon.textContent = '○';
                    }
                });

                // Actualizar label de fortaleza
                if (strengthLabel) {
                    strengthLabel.textContent = strengthTexts[passed] ?? '';
                    strengthLabel.style.color = strengthColors[passed] ?? '#9ca3af';
                }
            });
        }

        /* ---- Validación coincidencia ---- */
        const confirmInput = document.getElementById('confirm_password');
        const matchError = document.getElementById('matchError');

        if (confirmInput && pwdInput) { // 👈 VERIFICAR QUE EXISTEN
            confirmInput.addEventListener('input', checkMatch);
            pwdInput.addEventListener('input', checkMatch);

            function checkMatch() {
                if (confirmInput.value && confirmInput.value !== pwdInput.value) {
                    if (matchError) matchError.style.display = 'block';
                    confirmInput.style.borderColor = '#dc2626';
                } else {
                    if (matchError) matchError.style.display = 'none';
                    confirmInput.style.borderColor = '';
                }
            }
        }

        /* ---- Notificaciones ---- */
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        if (notificationBtn && notificationDropdown) { // 👈 VERIFICAR QUE EXISTEN
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.remove('show');
                }
            });

            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function() {
                    notificationDropdown.classList.remove('show');
                });
            });
        }
    </script>
</body>

</html>
