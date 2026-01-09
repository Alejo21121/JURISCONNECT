<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>JurisConnect SENA - Restablecer Contraseña</title>
    <link rel="stylesheet" href="{{ asset('/css/register.css') }}">
    <style>
        /* Estilos para las alertas */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
        }

        .alert-icon {
            font-size: 20px;
            font-weight: bold;
        }

        /* Cuadro de requisitos de contraseña */
        .password-requirements {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .password-requirements h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #495057;
            font-weight: 600;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 0;
            font-size: 13px;
            color: #6c757d;
            transition: color 0.3s ease;
        }

        .requirement-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            background-color: #e9ecef;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .requirement.valid {
            color: #28a745;
        }

        .requirement.valid .requirement-icon {
            background-color: #28a745;
            color: white;
        }

        .requirement.invalid {
            color: #dc3545;
        }
    </style>
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
            <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}" required autofocus>
            
            <!-- Password -->
            <label for="password">Nueva Contraseña</label>
            <div class="password-wrapper">
                <input id="password" type="password" name="password" required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <!-- Ojo cerrado -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeClosed-password"
                        viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.12 10.12 0 0 1 12 20c-7 0-11-8-11-8
                                a19.44 19.44 0 0 1 4.24-5.94M9.9 4.24A9.77 9.77 0 0 1 12 4
                                c7 0 11 8 11 8a19.44 19.44 0 0 1-4.24 5.94M1 1l22 22"/>
                    </svg>
                    <!-- Ojo abierto -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeOpen-password"
                        viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
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
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeClosed-password_confirmation"
                        viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.12 10.12 0 0 1 12 20c-7 0-11-8-11-8
                                a19.44 19.44 0 0 1 4.24-5.94M9.9 4.24A9.77 9.77 0 0 1 12 4
                                c7 0 11 8 11 8a19.44 19.44 0 0 1-4.24 5.94M1 1l22 22"/>
                    </svg>
                    <!-- Ojo abierto -->
                    <svg xmlns="http://www.w3.org/2000/svg" id="eyeOpen-password_confirmation"
                        viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
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

    <script>
        // ========== CONFIGURACIÓN DE TIEMPO DE EXPIRACIÓN ==========
        const TOKEN_EXPIRATION_TIME = 15 * 60 * 1000; // 15 minutos
        let tokenExpirationTimer;

        // ========== FUNCIÓN PARA MOSTRAR ALERTAS ==========
        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alert-container');
            
            // Remover alertas anteriores
            alertContainer.innerHTML = '';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            
            const icon = type === 'error' ? '✕' : '⚠';
            alertDiv.innerHTML = `
                <span class="alert-icon">${icon}</span>
                <span>${message}</span>
            `;
            
            alertContainer.appendChild(alertDiv);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }

        // ========== VALIDAR REQUISITOS DE CONTRASEÑA ==========
        function validatePasswordRequirements(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };

            // Actualizar UI de requisitos
            updateRequirement('req-length', requirements.length);
            updateRequirement('req-uppercase', requirements.uppercase);
            updateRequirement('req-lowercase', requirements.lowercase);
            updateRequirement('req-number', requirements.number);
            updateRequirement('req-special', requirements.special);

            // Retornar si todos los requisitos se cumplen
            return Object.values(requirements).every(req => req === true);
        }

        // ========== ACTUALIZAR UI DE REQUISITO ==========
        function updateRequirement(elementId, isValid) {
            const element = document.getElementById(elementId);
            const icon = element.querySelector('.requirement-icon');
            
            if (isValid) {
                element.classList.add('valid');
                element.classList.remove('invalid');
                icon.textContent = '✓';
            } else {
                element.classList.remove('valid');
                element.classList.add('invalid');
                icon.textContent = '○';
            }
        }

        // ========== INICIAR TEMPORIZADOR DE EXPIRACIÓN ==========
        function startTokenExpirationTimer() {
            tokenExpirationTimer = setTimeout(() => {
                showAlert('El enlace de restablecimiento ha expirado. Por favor, solicita uno nuevo.', 'warning');
                
                // Deshabilitar el formulario
                const form = document.getElementById('resetPasswordForm');
                const inputs = form.querySelectorAll('input');
                const submitBtn = form.querySelector('button[type="submit"]');
                
                inputs.forEach(input => input.disabled = true);
                submitBtn.disabled = true;
                submitBtn.textContent = 'Enlace Expirado';
                
                // Redirigir después de 3 segundos
                setTimeout(() => {
                    window.location.href = '/forgot-password';
                }, 3000);
            }, TOKEN_EXPIRATION_TIME);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ========== CÓDIGO ORIGINAL (Placeholder, efectos, etc.) ==========
            const emailInput = document.getElementById('email');
            const form = document.querySelector('form');
            const nextButton = document.querySelector('.btn-next');
            const backButton = document.querySelector('.btn-back');
            
            // Configuración de placeholders dinámicos
            let placeholderIndex = 0;
            const placeholders = [
                'xxxxxxxxxxxx',
                'tu.email@sena.edu.co',
                'correo@ejemplo.com',
                'usuario@dominio.com'
            ];

            // Función para cambiar placeholder
            function changePlaceholder() {
                if (emailInput && !emailInput.value && document.activeElement !== emailInput) {
                    placeholderIndex = (placeholderIndex + 1) % placeholders.length;
                    emailInput.placeholder = placeholders[placeholderIndex];
                }
            }

            // Cambiar placeholder cada 3 segundos
            setInterval(changePlaceholder, 3000);

            // Eventos del campo email
            if (emailInput) {
                emailInput.addEventListener('focus', function() {
                    this.placeholder = 'Ingresa tu correo electrónico';
                    this.style.textAlign = 'left';
                });

                emailInput.addEventListener('blur', function() {
                    if (!this.value) {
                        this.placeholder = 'xxxxxxxxxxxx';
                        this.style.textAlign = 'center';
                    }
                });

                emailInput.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    
                    if (this.value && !isValidEmail(this.value)) {
                        this.style.borderColor = '#ffc107';
                    } else if (this.value && isValidEmail(this.value)) {
                        this.style.borderColor = '#28a745';
                    } else {
                        this.style.borderColor = '#e9ecef';
                    }
                });
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function removeExistingAlerts() {
                const existingAlerts = document.querySelectorAll('.alert');
                existingAlerts.forEach(alert => {
                    if (!alert.querySelector('p')?.textContent.includes('Hemos enviado')) {
                        alert.remove();
                    }
                });
            }

            function animateElements() {
                const leftSection = document.querySelector('.left-section');
                const formContainer = document.querySelector('.form-container');
                
                if (leftSection) {
                    leftSection.style.opacity = '0';
                    leftSection.style.transform = 'translateX(-50px)';
                    
                    setTimeout(() => {
                        leftSection.style.transition = 'all 1s ease-out';
                        leftSection.style.opacity = '1';
                        leftSection.style.transform = 'translateX(0)';
                    }, 100);
                }
                
                if (formContainer) {
                    formContainer.style.opacity = '0';
                    formContainer.style.transform = 'translateX(50px)';
                    
                    setTimeout(() => {
                        formContainer.style.transition = 'all 1s ease-out';
                        formContainer.style.opacity = '1';
                        formContainer.style.transform = 'translateX(0)';
                    }, 300);
                }
            }

            function addButtonEffects() {
                const buttons = document.querySelectorAll('.btn');
                
                buttons.forEach(btn => {
                    btn.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-2px)';
                    });
                    
                    btn.addEventListener('mouseleave', function() {
                        if (!this.disabled) {
                            this.style.transform = 'translateY(0)';
                        }
                    });
                });
            }

            animateElements();
            addButtonEffects();

            // ========== NUEVAS FUNCIONALIDADES ==========
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const resetForm = document.getElementById('resetPasswordForm');

            // Iniciar temporizador de expiración
            startTokenExpirationTimer();

            // Validar contraseña en tiempo real
            passwordInput.addEventListener('input', function() {
                validatePasswordRequirements(this.value);
            });

            // Validar coincidencia de contraseñas
            confirmPasswordInput.addEventListener('input', function() {
                if (passwordInput.value && this.value) {
                    if (passwordInput.value !== this.value) {
                        this.style.borderColor = '#dc3545';
                    } else {
                        this.style.borderColor = '#28a745';
                    }
                }
            });

            // Manejar envío del formulario
            resetForm.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                // Validar que las contraseñas coincidan
                if (password !== confirmPassword) {
                    e.preventDefault();
                    showAlert('Las contraseñas no coinciden. Por favor, verifica que ambas contraseñas sean iguales.', 'error');
                    confirmPasswordInput.focus();
                    return;
                }

                // Validar requisitos de contraseña
                if (!validatePasswordRequirements(password)) {
                    e.preventDefault();
                    showAlert('La contraseña no cumple con todos los requisitos de seguridad.', 'error');
                    passwordInput.focus();
                    return;
                }

                // Si todo está bien, el formulario se enviará normalmente
            });

            console.log('JurisConnect - Recuperar Contraseña inicializado correctamente');
        });

        // ========== FUNCIÓN TOGGLE PASSWORD (ORIGINAL) ==========
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eyeClosed = document.getElementById(`eyeClosed-${inputId}`);
            const eyeOpen = document.getElementById(`eyeOpen-${inputId}`);

            if (input.type === "password") {
                input.type = "text";
                eyeClosed.style.display = "none";
                eyeOpen.style.display = "inline";
            } else {
                input.type = "password";
                eyeClosed.style.display = "inline";
                eyeOpen.style.display = "none";
            }
        }
        window.togglePassword = togglePassword;

        // Limpiar temporizador al salir
        window.addEventListener('beforeunload', function() {
            if (tokenExpirationTimer) {
                clearTimeout(tokenExpirationTimer);
            }
        });
    </script>
</body>
</html>