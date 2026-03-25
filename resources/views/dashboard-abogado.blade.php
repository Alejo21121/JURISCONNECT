<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-capable" content="yes">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;800&family=Poppins:wght@400;500;600;700&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </x-slot>

    <div class="dashboard-wrapper">

        <div class="overlay" id="overlay"></div>

        {{-- COMPONENTE SIDEBAR --}}
        <x-sidebar />

        <main class="main-content" id="mainContent">

            @php
                $role = Auth::user()->role_id;
                $isAbogado = $role == 2;
                $isAsistente = $role == 3;
                $isAbogadoS = $role == 4;

            @endphp

            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <h1>
                        @if ($isAbogado)
                            Panel del Abogado
                        @elseif($isAsistente)
                            Panel del Asistente Jurídico
                        @elseif($isAbogadoS)
                            Panel del Abogado Supervisor
                        @else
                            Panel del Usuario
                        @endif
                    </h1>
                </div>
                <div class="header-right">
                    <x-notification-dropdown />
                    <a href="{{ route('dashboard.abogado') }}">
                        <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
                    </a>
                </div>
            </header>

            {{-- ===== HERO DE BIENVENIDA ===== --}}
            <section class="hero-welcome">
                <div class="hero-bg-circles">
                    <div class="hero-circle hero-circle--1"></div>
                    <div class="hero-circle hero-circle--2"></div>
                </div>
                <div class="hero-content">
                    <span class="hero-tag">
                        <span class="hero-dot"></span>
                        Sistema jurídico activo
                    </span>
                    <h1 class="hero-title">
                        Bienvenido, <span class="hero-name">{{ auth()->user()->name }}</span>
                    </h1>
                    <p class="hero-sub">
                        @if ($isAbogado)
                            Gestiona tus procesos y conceptos jurídicos desde aquí.
                        @elseif($isAsistente)
                            Apoya la gestión de procesos y actividades jurídicas asignadas.
                        @else
                            Bienvenido al sistema jurídico.
                        @endif
                    </p>
                    <div class="hero-actions">
                        <a href="{{ route('mis.procesos') }}" class="hero-btn-primary">Ver mis procesos →</a>
                        <a href="{{ route('conceptos.create') }}" class="hero-btn-ghost">Ver conceptos</a>
                    </div>
                </div>
            </section>

            {{-- ===== SECCIÓN TÍTULO CARDS ===== --}}
            <div class="section-heading">
                <p class="section-label">Accesos rápidos</p>
                <h2 class="section-title">¿Qué necesitas hacer hoy?</h2>
            </div>

            {{-- ===== CARDS ===== --}}
            <div class="cards-container">

                {{-- Card: Registrar Proceso --}}
                <div class="dashboard-card">
                    <div class="card-visual card-visual--registrar">
                        <div class="card-icon-big">⚖️</div>
                    </div>
                    <div class="card-body">
                        <p class="card-cat">Expedientes</p>
                        <h3 class="card-titles">Registrar Proceso</h3>
                        <p class="card-desc">Inicia un nuevo expediente jurídico y asígnale los detalles
                            correspondientes.</p>
                        @if (in_array(auth()->user()->role_id, [2, 4]))
                            <a href="{{ route('legal_processes.create') }}" class="card-btn btn-green">Registrar →</a>
                        @else
                            <span class="no-perm-badge">
                                <i class="fa-solid fa-lock" style="font-size:11px;"></i>
                                Sin permisos
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Card: Mis Procesos --}}
                <div class="dashboard-card">
                    <div class="card-visual card-visual--procesos">
                        <div class="card-icon-big delay-1">📂</div>
                    </div>
                    <div class="card-body">
                        <p class="card-cat">Seguimiento</p>
                        <h3 class="card-titles">Mis Procesos</h3>
                        <p class="card-desc">Consulta, actualiza y da seguimiento a los procesos asignados.</p>
                        <a href="{{ route('mis.procesos') }}" class="card-btn btn-blue">Ver Procesos →</a>
                    </div>
                </div>

                {{-- Card: Conceptos Jurídicos --}}
                <div class="dashboard-card">
                    <div class="card-visual card-visual--conceptos">
                        <div class="card-icon-big delay-2">📋</div>
                    </div>
                    <div class="card-body">
                        <p class="card-cat">Doctrina</p>
                        <h3 class="card-titles">Conceptos Jurídicos</h3>
                        <p class="card-desc">Gestiona conceptos jurídicos de manera organizada.</p>
                        <a href="{{ route('conceptos.create') }}" class="card-btn btn-amber">Ver Conceptos →</a>
                    </div>
                </div>

            </div>

            {{-- ===== SECCIÓN CÓMO FUNCIONA ===== --}}
            <div class="section-heading">
                <p class="section-label">Flujo de trabajo</p>
                <h2 class="section-title">¿Cómo funciona el sistema?</h2>
            </div>

            <div class="how-grid">
                <div class="how-step">
                    <div class="how-num">1</div>
                    <h4 class="how-step-title">Ingresas al sistema</h4>
                    <p class="how-step-desc">Accedes con tu usuario y el sistema carga tus permisos y procesos asignados
                        automáticamente.</p>
                </div>
                <div class="how-step">
                    <div class="how-num">2</div>
                    <h4 class="how-step-title">Revisas tus procesos</h4>
                    <p class="how-step-desc">Consultas el estado de cada expediente, actualizas información y dejas
                        registro de las novedades.</p>
                </div>
                <div class="how-step">
                    <div class="how-num">3</div>
                    <h4 class="how-step-title">Emites conceptos</h4>
                    <p class="how-step-desc">Redactas y archivas conceptos jurídicos organizados por categoría, fecha y
                        proceso relacionado.</p>
                </div>
                <div class="how-step">
                    <div class="how-num">4</div>
                    <h4 class="how-step-title">Recibes notificaciones</h4>
                    <p class="how-step-desc">El sistema te alerta sobre cambios, vencimientos de términos y tareas
                        pendientes en tiempo real.</p>
                </div>
            </div>

        </main>
    </div>

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

</x-app-layout>
