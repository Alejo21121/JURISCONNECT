<!DOCTYPE html>
<html lang="es"> <!-- pagina para ver los procesos asignados al abogado -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/procesos_judiciales.css') }}">
    <title>Procesos Judiciales</title>
</head>

<body>

    <!-- Modal para ver datos del proceso -->
    <div id="viewProcessModal" class="modal" style="display:none;">
        <div class="modal-content">
            <!-- Header con botón de cierre -->
            <div class="modal-header-custom">
                <h2>
                    <i class="fas fa-gavel"></i>
                    Datos del Proceso
                </h2>
            </div>

            <!-- Body -->
            <div class="modal-body-custom" id="processModalBody">
                <p>Cargando datos...</p>
            </div>
        </div>
    </div>

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
                        <h1>Procesos Judiciales</h1>
                        <p>Gestión integral de casos legales</p>
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
                    <div class="info-text">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Administre sus procesos judiciales desde esta vista</span>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('procesos.export.excel') }}" class="btn-success">EXPORTAR EXCEL</a>
                        <a href="{{ route('procesos.export.pdf') }}" class="btn-danger">EXPORTAR PDF</a>
                    </div>

                    <a href="{{ route('pagos.index') }}" class="btn btn-pagos">
                        <i class="fas fa-save"></i>
                        Ver Pagos $
                    </a>

                    <div class="button-group">
                        <a href="{{ route('dashboard.abogado') }}" class="btn btn-secondary">
                            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver al Dashboard
                        </a>

                        @if (Auth::user()->role_id == 2)
                            <a href="{{ route('procesos.create') }}" class="btn btn-primary">
                                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Nuevo Proceso
                            </a>
                        @else
                            <button class="btn btn-primary" disabled style="background: gray; cursor:not-allowed;">
                                No tienes permisos
                            </button>
                        @endif

                        </a>
                    </div>
                </div>
                <div class="search-section">
                    <input type="text" id="searchInput" class="form-control mb-3"
                        placeholder="Buscar por nombre, apellido o número de radicado">
                </div>

            </div>
        </div>
        <div id="procesosTableContainer">
            @include('profile.partials.processes-table', ['procesos' => $procesos])
        </div>
    </div>

    <script>
        window.routes = {
            procesosIndex: "{{ route('procesos.index') }}"
        };
    </script>

    <script src="{{ asset('js/indexPro.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
