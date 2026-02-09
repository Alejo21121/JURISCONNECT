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

                    <!-- TÍTULO CAMBIANTE -->
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

                <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
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

</x-app-layout>
