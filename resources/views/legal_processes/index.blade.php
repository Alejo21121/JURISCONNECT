<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/procesos_judiciales.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- ✅ DESPUÉS: solo el JS, sin el CSS de Tailwind --}}
    @vite(['resources/js/dash.js'])
    <title>Procesos Judiciales</title>
</head>

<body>
    {{--  WRAPPER PRINCIPAL --}}
    <div class="dashboard-wrapper">
        <div class="overlay" id="overlay"></div>

        {{--  SIDEBAR REUTILIZABLE --}}
        <x-sidebar />

        {{--  CONTENIDO PRINCIPAL --}}
        <main class="main-content" id="mainContent">

            {{-- Header con hamburguesa y notificaciones --}}
            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <h1>Procesos Judiciales</h1>
                </div>
                <div class="header-right">
                    {{--  COMPONENTE DE NOTIFICACIONES --}}
                    <x-notification-dropdown />

                    <!-- Logo SENA -->
                    <a href="{{ route('dashboard.abogado') }}">
                        <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
                    </a>
                </div>
            </header>

            {{-- Modal para ver datos del proceso --}}
            <div id="viewProcessModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header-custom">
                        <h2>
                            <i class="fas fa-gavel"></i>
                            Datos del Proceso
                        </h2>
                    </div>
                    <div class="modal-body-custom" id="processModalBody">
                        <p>Cargando datos...</p>
                    </div>
                </div>
            </div>

            {{-- Contenedor principal --}}
            <div class="container">
                <!-- Header Principal -->
                <div class="main-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="header-text">
                                <h1>Gestión integral de casos legales</h1>
                            </div>
                        </div>
                        <div class="header-stats">
                            <span class="stats-label">Total:</span>
                            <span class="pagination-info" id="totalCount">{{ $procesos->total() }}</span>
                        </div>
                    </div>

                    <!-- Mensaje de éxito (ejemplo) -->
                    <div class="success-message" style="display: none;">
                        <svg class="success-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                        </svg>
                        <p class="success-text">Proceso creado exitosamente</p>
                    </div>

                    <!-- Barra de acciones -->
                    <div class="actions-bar">
                        <div class="actions-content">
                            <!-- Lado izquierdo: Info -->
                            <div class="info-section">
                                <div class="info-text">
                                    <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Administre sus procesos judiciales desde esta vista</span>
                                </div>
                            </div>

                            <!-- Lado derecho: Todos los botones -->
                            <div class="button-group">
                                <div class="export-group">
                                    <a href="{{ route('procesos.export.excel') }}" class="btn btn-success">EXPORTAR
                                        EXCEL</a>
                                    <a href="{{ route('procesos.export.pdf') }}" class="btn btn-danger">EXPORTAR PDF</a>
                                </div>

                                <div class="main-actions">
                                    <a href="{{ route('pagos.index') }}" class="btn btn-primary">Ver Pagos</a>
                                    <a href="{{ route('dashboard.abogado') }}" class="btn btn-secondary">
                                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                        </svg>
                                        Volver al Dashboard
                                    </a>

                                    @if (in_array(Auth::user()->role_id, [2, 4]))
                                        <a href="{{ route('procesos.create') }}" class="btn btn-primary">
                                            <svg class="btn-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Nuevo Proceso
                                        </a>
                                    @else
                                        <button class="btn btn-primary" disabled
                                            style="background: gray; cursor:not-allowed;">
                                            No tienes permisos
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Barra de búsqueda (debajo de los botones) -->
                        <div class="search-section">
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Buscar por nombre, apellido o número de radicado">
                        </div>
                    </div>
                    <div id="procesosTableContainer">
                        @include('profile.partials.processes-table', ['procesos' => $procesos])
                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>

    <script>
        window.routes = {
            procesosIndex: "{{ route('procesos.index') }}"
        };

        //  DETECTAR SI VIENE DE NOTIFICACIÓN DE PROCESO
        const urlParams = new URLSearchParams(window.location.search);
        const procesoIdNotificacion = urlParams.get('proceso');
        const markReadId = urlParams.get('mark_read');

        if (procesoIdNotificacion) {
            // Buscar automáticamente por ese proceso
            const searchInput = document.getElementById('searchInput');

            // Hacer la búsqueda del proceso
            fetch(`/procesos/${procesoIdNotificacion}`)
                .then(res => res.json())
                .then(data => {
                    // Buscar por radicado
                    searchInput.value = data.numero_radicado;
                    searchInput.dispatchEvent(new Event('input'));

                    // Esperar a que se filtre la tabla
                    setTimeout(() => {
                        // Buscar la fila del proceso
                        const rows = document.querySelectorAll('tbody tr');

                        rows.forEach(row => {
                            const radicadoCell = row.querySelector('td:first-child');

                            if (radicadoCell && radicadoCell.textContent.trim() === data
                                .numero_radicado) {

                                // Hacer scroll hacia la fila
                                row.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                // Quitar resaltado después de 5 segundos
                                setTimeout(() => {
                                    row.style.border = '';
                                    row.style.backgroundColor = '';
                                    row.style.boxShadow = '';
                                }, 5000);
                            }
                        });
                    }, 500);
                })
                .catch(error => {
                    console.error('Error al buscar proceso:', error);
                });
        }

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

    <script src="{{ asset('js/indexPro.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>

</html>
