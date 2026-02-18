<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/js/dash.js'])
    <title>Mi Perfil</title>
</head>

<body>
    <div class="dashboard-wrapper">
        <div class="overlay" id="overlay"></div>

        {{-- SIDEBAR --}}
        <x-sidebar />

        <main class="main-content" id="mainContent">

            {{-- Header --}}
            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <h1>Mi Perfil</h1>
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

                    {{-- Encabezado con avatar --}}
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="{{ Auth::user()->foto_perfil
                                ? asset('storage/' . Auth::user()->foto_perfil)
                                : asset('img/sena-servicio-nacional-de-aprendizaje_1747692754.jpeg') }}"
                                alt="Foto de perfil">
                        </div>

                        <h2>{{ $user->name }}</h2>

                        <p class="profile-role">
                            @if ($user->role_id == 1)
                                <span class="profile-role-badge"><i class="fas fa-shield-alt"></i> Administrador</span>
                            @elseif($user->role_id == 2)
                                <span class="profile-role-badge"><i class="fas fa-briefcase"></i> Abogado</span>
                            @elseif($user->role_id == 3)
                                <span class="profile-role-badge"><i class="fas fa-balance-scale"></i> Asistente
                                    Jurídico</span>
                            @endif
                        </p>
                    </div>

                    {{-- Información personal --}}
                    <div class="profile-info">
                        <h3><i class="fas fa-user-circle"></i> Información Personal</h3>

                        <div class="info-grid">
                            @if ($additionalData)
                                <div class="info-item">
                                    <label><i class="fas fa-user"></i> Nombre</label>
                                    <input type="text" value="{{ $additionalData->nombre }}" readonly>
                                </div>

                                <div class="info-item">
                                    <label><i class="fas fa-user"></i> Apellido</label>
                                    <input type="text" value="{{ $additionalData->apellido }}" readonly>
                                </div>

                                <div class="info-item">
                                    <label><i class="fas fa-id-card"></i> Tipo de Documento</label>
                                    <input type="text" value="{{ $additionalData->tipo_documento }}" readonly>
                                </div>

                                <div class="info-item">
                                    <label><i class="fas fa-id-badge"></i> Número de Documento</label>
                                    <input type="text" value="{{ $additionalData->numero_documento }}" readonly>
                                </div>

                                <div class="info-item">
                                    <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                                    <input type="email" value="{{ $additionalData->correo }}" readonly>
                                </div>

                                @if ($additionalData->telefono)
                                    <div class="info-item">
                                        <label><i class="fas fa-phone"></i> Teléfono</label>
                                        <input type="text" value="{{ $additionalData->telefono }}" readonly>
                                    </div>
                                @endif

                                @if ($user->role_id == 2 && $additionalData->especialidad)
                                    <div class="info-item">
                                        <label><i class="fas fa-briefcase"></i> Especialidad</label>
                                        <input type="text" value="{{ $additionalData->especialidad }}" readonly>
                                    </div>
                                @endif
                            @else
                                <div class="info-item">
                                    <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                                    <input type="email" value="{{ $user->email }}" readonly>
                                </div>
                            @endif
                        </div>

                        <div class="profile-actions">
                            <a href="{{ route('profile.password.edit') }}" class="btn btn-primary">
                                <i class="fas fa-key"></i> Cambiar Contraseña
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showCustomAlert('success', '¡Éxito!', '{{ session('success') }}');
            });
        </script>
    @endif

    <script>
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

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
    </script>
</body>

</html>