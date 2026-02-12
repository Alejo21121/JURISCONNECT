<x-app-layout>
    <x-slot name="header">
        <!-- Meta tags y links van AQUÍ dentro del slot header -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-capable" content="yes">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    </x-slot>

    <div class="dashboard-wrapper">

        <div class="overlay" id="overlay"></div>

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar" id="sidebar">
            <div class="profile">

                <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png" style="display: none;">

                <div id="loadingIndicator"
                    style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                    background: rgba(0,0,0,0.7); color: white; padding: 10px; border-radius: 5px; z-index: 1000;">
                    Subiendo...
                </div>

                <div class="profile-pic profile-pic-clickable" onclick="document.getElementById('fileInput').click();"
                    title="Haz clic para cambiar tu foto">
                    <img src="{{ Auth::user()->foto_perfil ? asset('storage/' . Auth::user()->foto_perfil) : asset('img/silueta-atardecer-foto-perfil.webp') }}"
                        id="profileImage" alt="Foto de perfil">
                </div>

                <h3>{{ Auth::user()->name }}</h3>
                <p>{{ Auth::user()->email }}</p>
            </div>

            <nav class="nav-menu"></nav>

            <div class="sidebar-footer">
                <div class="sena-logo">
                    <img src="{{ asset('img/LogoInsti.png') }}" alt="Logo SENA">
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="main-content" id="mainContent">

            @php
                $role = Auth::user()->role_id;
                $isAbogado = $role == 2;
                $isAsistente = $role == 3;
            @endphp

            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <h1>
                        @if ($isAbogado)
                            Panel del Abogado
                        @elseif($isAsistente)
                            Panel del Asistente Jurídico
                        @else
                            Panel del Usuario
                        @endif
                    </h1>
                </div>

                <!-- 👇 NUEVO CONTENEDOR DERECHO -->
                <div class="header-right">
                    <!-- Notificaciones -->
                    @php
                        $notificaciones = auth()->user()->unreadNotifications;
                    @endphp

                    <div class="notification-wrapper">
                        <button class="notification-btn" id="notificationBtn">
                            <i class="fas fa-bell"></i>
                            @if ($notificaciones->count() > 0)
                                <span class="notification-badge">
                                    {{ $notificaciones->count() }}
                                </span>
                            @endif
                        </button>

                        <div class="notification-dropdown" id="notificationDropdown">
                            @if ($notificaciones->count() > 0)
                                <div class="notification-header">
                                    <span>Notificaciones</span>
                                    <span class="notification-count">
                                        {{ $notificaciones->count() }}
                                    </span>
                                </div>

                                @foreach ($notificaciones as $noti)
                                    <a href="{{ route('pagos.index') }}?proceso={{ $noti->data['proceso_id'] }}&mark_read={{ $noti->id }}"
                                        class="notification-item">
                                        <div class="notification-content">
                                            <strong>{{ $noti->data['titulo'] }}</strong>
                                            <br>
                                            <small>{{ $noti->data['mensaje'] }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="notification-empty">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>No tienes notificaciones</p>
                                </div>
                            @endif
                        </div>
                    </div>


                    <!-- Logo SENA -->
                    <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
                </div>
            </header>

            <!-- MENSAJE DE BIENVENIDA DINÁMICO -->
            <div class="w-full text-center px-4 mt-4 md:mt-10">
                <p class="text-gray-700 text-lg md:text-xl font-medium leading-snug">
                    Bienvenido, <span class="font-semibold text-green-700">{{ auth()->user()->name }}</span>.
                    @if ($isAbogado)
                        Gestiona tus procesos y conceptos jurídicos desde aquí.
                    @elseif($isAsistente)
                        Apoya la gestión de procesos y actividades jurídicas asignadas.
                    @else
                        Bienvenido al sistema jurídico.
                    @endif
                </p>
            </div>

            <!-- ===== CARDS ===== -->
            <div class="cards-container">

                <!-- Registrar Proceso (solo abogado) -->
                <div class="dashboard-card">
                    <div class="card-icon">⚖️</div>
                    <h3>Registrar Proceso</h3>
                    <p>Inicia un nuevo expediente jurídico y asígnale los detalles correspondientes.</p>

                    @if ($isAbogado)
                        <a href="{{ route('legal_processes.create') }}">Registrar</a>
                    @else
                        <span style="color: red; font-weight:bold;">No tienes permisos</span>
                    @endif
                </div>

                <!-- Mis Procesos -->
                <div class="dashboard-card">
                    <div class="card-icon">📂</div>
                    <h3>Mis Procesos</h3>
                    <p>Consulta, actualiza y da seguimiento a los procesos asignados.</p>
                    <a href="{{ route('mis.procesos') }}">Ver Procesos</a>
                </div>

                <!-- Conceptos Jurídicos -->
                <div class="dashboard-card">
                    <div class="card-icon">✍️</div>
                    <h3>Conceptos Jurídicos</h3>
                    <p>Gestiona conceptos jurídicos de manera organizada.</p>
                    <a href="{{ route('conceptos.create') }}">Ver Conceptos</a>
                </div>

            </div>
        </main>

    </div>

    <script>
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });

        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });

        // Cerrar al hacer clic en una notificación
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                notificationDropdown.classList.remove('show');
            });
        });
    </script>


</x-app-layout>
