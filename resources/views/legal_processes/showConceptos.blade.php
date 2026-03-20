<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
    @vite(['resources/js/dash.js'])
    <title>Procesos Pendientes - CSS Puro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/showCon.css') }}">

    <!-- AGREGAR SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/showCon.js') }}" defer></script>
</head>

<body>
    <div class="dashboard-wrapper">
        <div class="overlay" id="overlay"></div>

        {{-- SIDEBAR REUTILIZABLE --}}
        <x-sidebar />

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="main-content" id="mainContent">

            {{-- Header con hamburguesa y notificaciones --}}
            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <i class="fas fa-balance-scale" style="color:#28a745; font-size:25px; margin-right:10px;"></i>
                    <h1>Sistema Jurídico</h1>
                </div>
                <div class="header-right">
                    {{-- COMPONENTE DE NOTIFICACIONES --}}
                    <x-notification-dropdown />

                    <!-- Logo SENA -->
                    <a href="{{ route('dashboard.abogado') }}">
                        <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
                    </a>
                </div>
            </header>

            <div class="container">
                <!-- Header -->
                <div class="section-header">
                    <div class="header-content">
                        <h1>Procesos Pendientes de Concepto Jurídico</h1>
                        <p>Gestiona los procesos que requieren análisis jurídico</p>
                    </div>
                    <!-- Buscador moderno -->
                    <form onsubmit="return false;">
                        <div class="search-wrapper">
                            <div class="search-group">
                                <input type="text" id="searchInput" name="search" class="search-input-modern"
                                    placeholder="Buscar por #, radicado o fecha..." value="{{ request('search') }}">
                                <button type="submit" class="search-button-modern">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($abogadosList->isNotEmpty())
                        <div class="filter-wrapper" style="margin-top: 12px;">
                            <div class="search-group">
                                <select id="abogadoFilter" class="search-input-modern" style="cursor:pointer;">
                                    <option value="">— Todos los abogados —</option>
                                    @foreach ($abogadosList as $lawyer)
                                        <option value="{{ $lawyer->id }}"
                                            {{ request('abogado_id') == $lawyer->id ? 'selected' : '' }}>
                                            {{ $lawyer->user->name ?? 'Abogado #' . $lawyer->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <a class="cancel-btn" href="{{ route('dashboard.abogado') }}">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                </div>

                <!-- Alerta de éxito (oculta por defecto) -->
                <div id="success-alert" class="alert alert-success hidden">
                    <i class="fas fa-check-circle"></i>
                    <span>Operación realizada exitosamente.</span>
                    <button class="alert-close" onclick="closeAlert('success-alert')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>


                <!-- Info de procesos pendientes -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <p class="font-bold">Procesos Pendientes</p>
                    </div>
                </div>

                <!-- Lista de Procesos -->
                <div id="processContainer">
                    @include('profile.partials.process-card', ['procesos' => $procesos])
                </div>

                <!-- Recordatorio -->
                <div class="reminder">
                    <div class="reminder-content">
                        <div class="reminder-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="reminder-text">
                            <h4>Recordatorio Importante</h4>
                            <p>Los conceptos jurídicos deben ser claros, precisos y fundamentados en la normatividad
                                vigente.
                                Asegúrate de incluir todas las referencias legales pertinentes y un análisis detallado
                                del caso.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para ver detalles del proceso -->
            <div id="viewProcessModal" class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Detalles del Proceso</h2>
                        <button class="modal-close" onclick="closeProcessModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="processModalBody" class="modal-body">
                        <!-- Contenido cargado dinámicamente -->
                    </div>
                    <div class="modal-footer">
                        <button class="cancel-btn" onclick="closeProcessModal()">Cerrar</button>
                    </div>
                </div>
            </div>
    </div>

</body>

</html>
