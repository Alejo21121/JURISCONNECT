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
    <div id="viewProcessModal" class="modal"
        style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); align-items:center; justify-content:center;">
        <div class="modal-content"
            style="background:white; border-radius:16px; max-width:500px; margin:auto; padding:2rem; position:relative;">
            <span class="close-button" onclick="closeProcessModal()"
                style="position:absolute; top:1rem; right:1rem; font-size:2rem; cursor:pointer;">&times;</span>
            <h2 style="font-size:1.25rem; font-weight:600; margin-bottom:1rem;">Datos del Proceso </h2>
            <div id="processModalBody">
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
                                <i class="fas fa-save"></i>
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
            @include('profile.partials.processes-table', ['proceso' => $procesos])
        </div>
    </div>

    <script>
        let searchTimeout;

        // Inicializar eventos de búsqueda
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById("searchInput");
            const searchBtn = document.getElementById("searchBtn");

            // Búsqueda al escribir (con debounce)
            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    clearTimeout(searchTimeout);
                    const searchTerm = this.value.trim();
                    searchTimeout = setTimeout(() => {
                        performSearch(searchTerm);
                    }, 300);
                });

                // Búsqueda al presionar ENTER
                searchInput.addEventListener("keypress", function(event) {
                    if (event.key === 'Enter') {
                        clearTimeout(searchTimeout);
                        performSearch(this.value.trim());
                    }
                });
            }

            // Búsqueda al hacer click en botón
            if (searchBtn) {
                searchBtn.addEventListener("click", function() {
                    const searchTerm = searchInput.value.trim();
                    performSearch(searchTerm);
                });
            }
        });

        // Función principal de búsqueda AJAX
        function performSearch(searchTerm) {
            const params = new URLSearchParams();
            if (searchTerm) {
                params.append('search', searchTerm);
            }
            params.append('ajax', '1');

            fetch(`{{ route('procesos.index') }}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.html) {
                        // Actualizar contenedor de tabla
                        document.getElementById('procesosTableContainer').innerHTML = data.html;

                        // Actualizar total de resultados
                        if (data.total !== undefined) {
                            document.getElementById('totalCount').textContent = data.total;
                        }
                    } else {
                        console.error('Error en búsqueda:', data.message || 'Error desconocido');
                    }
                })
                .catch(error => {
                    console.error('Error en la petición AJAX:', error);
                });
        }

        // Función para limpiar búsqueda
        function clearSearch() {
            const searchInput = document.getElementById("searchInput");
            if (searchInput) {
                searchInput.value = '';
                performSearch('');
            }
        }

        // Función para ver los datos del proceso en un modal

        function openProcessModal(id) {
            document.getElementById('viewProcessModal').style.display = 'flex';
            const body = document.getElementById('processModalBody');
            body.innerHTML = '<p>Cargando datos...</p>';

            fetch(`/procesos/${id}`)
                .then(res => res.json())
                .then(data => {

                    body.innerHTML = `
    <div class="process-details">

        <div class="detail-row">
            <span class="label">Radicado</span>
            <span class="value">${data.numero_radicado}</span>
        </div>

        <div class="detail-row">
            <span class="label">Fecha de radicación</span>
            <span class="value">${data.created_at}</span>
        </div>

        <div class="detail-row">
            <span class="label">Estado</span>
            <span class="value badge">${data.estado}</span>
        </div>

        <div class="detail-row">
            <span class="label">Tipo de proceso</span>
            <span class="value">${data.tipo_proceso}</span>
        </div>

        <div class="detail-row">
            <span class="label">Demandante</span>
            <span class="value">${data.demandante}</span>
        </div>

        <div class="detail-row">
            <span class="label">Demandado</span>
            <span class="value">${data.demandado}</span>
        </div>

        <div class="detail-box scroll-box">
            <span class="label">Detalle del caso</span>
           <p style="pre-wrap; word-wrap: break-word;">${data.descripcion ?? 'Sin descripción'} </p>
        </div>

        <hr>

    <h4 class="docs-title">📎 Documentos del proceso</h4>

    ${renderDocuments(data.documentos)}

</div>
`;

                })
                .catch(() => {
                    body.innerHTML = '<p>Error al cargar los datos.</p>';
                });
        }

        function renderDocuments(documentos) {
            if (!documentos || documentos.length === 0) {
                return `<p class="text-muted">No hay documentos asociados.</p>`;
            }

            return `
        <ul class="documents-list">
            ${documentos.map(doc => `
                                        <li>
                                            <i class="fas fa-file-pdf"></i>
                                            <a href="/storage/${doc.ruta}" target="_blank">
                                                ${doc.nombre}
                                            </a>
                                        </li>
                                    `).join('')}
        </ul>
    `;
        }

        function closeProcessModal() {
            document.getElementById('viewProcessModal').style.display = 'none';
        }

        //  Cerrar modal con la tecla ESC
        document.addEventListener('keydown', function(event) {
            const modal = document.getElementById('viewProcessModal');
            if (event.key === 'Escape' && modal.style.display === 'flex') {
                closeProcessModal();
            }
        });

        function confirmDelete(id, nombre) {
            Swal.fire({
                title: 'Confirmar Eliminación',
                html: `¿Estás seguro de eliminar el proceso de <b>${nombre}</b>?<br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                customClass: {
                    popup: 'custom-popup',
                    title: 'custom-title',
                    htmlContainer: 'custom-text',
                    confirmButton: 'custom-confirm',
                    cancelButton: 'custom-cancel',
                    icon: 'custom-icon'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>
