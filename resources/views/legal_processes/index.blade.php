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

            if (searchInput) {
                searchInput.addEventListener("input", function() {
                    clearTimeout(searchTimeout);
                    const searchTerm = this.value.trim();
                    searchTimeout = setTimeout(() => {
                        performSearch(searchTerm);
                    }, 300);
                });

                searchInput.addEventListener("keypress", function(event) {
                    if (event.key === 'Enter') {
                        clearTimeout(searchTimeout);
                        performSearch(this.value.trim());
                    }
                });
            }

            if (searchBtn) {
                searchBtn.addEventListener("click", function() {
                    const searchTerm = searchInput.value.trim();
                    performSearch(searchTerm);
                });
            }
        });

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
                        document.getElementById('procesosTableContainer').innerHTML = data.html;
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

        function clearSearch() {
            const searchInput = document.getElementById("searchInput");
            if (searchInput) {
                searchInput.value = '';
                performSearch('');
            }
        }

        // ========================================
        // MODAL DE PROCESO - VERSIÓN MEJORADA
        // ========================================
        function openProcessModal(id) {
            const modal = document.getElementById('viewProcessModal');
            const body = document.getElementById('processModalBody');

            modal.style.display = 'flex';
            body.innerHTML = '<p style="text-align:center; padding:2rem; color:#64748b;">⏳ Cargando datos...</p>';

            fetch(`/procesos/${id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Error al cargar datos');
                    return res.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);

                    body.innerHTML = `
<div class="process-details">

    <!-- INFORMACIÓN BÁSICA -->
    <div class="detail-row">
        <span class="label">📋 Radicado</span>
        <span class="value"><strong>${data.numero_radicado}</strong></span>
    </div>

    <div class="detail-row">
        <span class="label">📅 Fecha radicación</span>
        <span class="value">${data.created_at}</span>
    </div>

    <div class="detail-row">
        <span class="label">📊 Estado</span>
        <span class="value">
            <span class="badge estado-${data.estado.toLowerCase().replace(/ /g, '-')}">
                ${getEstadoIcon(data.estado)} ${data.estado}
            </span>
        </span>
    </div>

    <div class="detail-row">
        <span class="label">⚖️ Tipo de proceso</span>
        <span class="value">${data.tipo_proceso}</span>
    </div>

    <div class="detail-row">
        <span class="label">👤 Demandante</span>
        <span class="value">${data.demandante}</span>
    </div>

    <div class="detail-row">
        <span class="label">👥 Demandado</span>
        <span class="value">${data.demandado}</span>
    </div>

    <!-- DESCRIPCIÓN -->
    <div class="detail-box">
        <span class="label">📝 Detalle del caso</span>
        <p style="white-space: pre-wrap; word-wrap: break-word;">${data.descripcion || 'Sin descripción'}</p>
    </div>

    <div class="section-divider"></div>

    <!-- INFORMACIÓN DE PAGO -->
    <h4 class="section-title">💰 Información de Pago</h4>

    <div class="detail-row">
        <span class="label">¿Requiere pago?</span>
        <span class="value">
            ${data.requiere_pago == 1 
                ? '<strong style="color:#059669;">✅ Sí</strong>' 
                : '<strong style="color:#6b7280;">❌ No</strong>'}
        </span>
    </div>

    ${data.requiere_pago == 1 ? `
                        <div class="detail-row">
                            <span class="label">Valor estimado</span>
                            <span class="value">
                                <strong style="color:#2563eb; font-size:1.125rem;">
                                    $ ${Number(data.valor_estimado || 0).toLocaleString('es-CO')}
                                </strong>
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="label">Estado del pago</span>
                            <span class="value">
                                ${data.pago_realizado === true
                                    ? '<span class="badge pago-realizado"><i class="fas fa-check-circle"></i> Pago realizado</span>'
                                    : '<span class="badge pago-pendiente"><i class="fas fa-clock"></i> Pago pendiente</span>'}
                            </span>
                        </div>

                        ${data.pago_realizado === true && data.pago ? `
            <div class="pago-card">
                <h5 style="color:#065f46; font-size:1rem; margin:0 0 1rem 0; font-weight:700; display:flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-money-bill-wave"></i>
                    Detalles del Pago Realizado
                </h5>
                <div class="pago-grid">
                    <div class="pago-item">
                        <span class="label">Valor Pagado</span>
                        <span class="value">$ ${Number(data.pago.valor_pagado || 0).toLocaleString('es-CO')}</span>
                    </div>
                    <div class="pago-item">
                        <span class="label">Fecha de Pago</span>
                        <span class="value"><i class="fas fa-calendar-check"></i> ${data.pago.fecha_pago || 'N/A'}</span>
                    </div>
                    <div class="pago-item">
                        <span class="label">Forma de Pago</span>
                        <span class="value">${data.pago.forma_pago || 'N/A'}</span>
                    </div>
                    ${data.pago.observaciones ? `
                                        <div class="pago-item" style="grid-column: 1 / -1;">
                                            <span class="label">Observaciones</span>
                                            <span class="value" style="font-size:0.9rem; font-weight:500; color:#374151;">${data.pago.observaciones}</span>
                                        </div>
                                    ` : ''}
                </div>
            </div>
        ` : ''}
                    ` : `
                        <div class="detail-row">
                            <span class="label">Estado del pago</span>
                            <span class="value">
                                <span class="badge pago-no-aplica">— No aplica</span>
                            </span>
                        </div>
                    `}

    <div class="section-divider"></div>

    <!-- DOCUMENTOS -->
    <h4 class="section-title">
        <i class="fas fa-paperclip"></i>
        Documentos del proceso
    </h4>
    ${renderDocuments(data.documentos)}

</div>
`;
                })
                .catch(error => {
                    console.error('Error:', error);
                    body.innerHTML = `
                    <div style="text-align:center; padding:2rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size:3rem; color:#dc2626; margin-bottom:1rem;"></i>
                        <p style="color:#dc2626; font-weight:600;">❌ Error al cargar los datos</p>
                        <p style="color:#64748b; font-size:0.875rem; margin-top:0.5rem;">${error.message}</p>
                    </div>
                `;
                });
        }

        // Cerrar modal
        function closeProcessModal() {
            document.getElementById('viewProcessModal').style.display = 'none';
        }

        // 🔥 CERRAR MODAL AL HACER CLIC FUERA
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('viewProcessModal');

            // Si el modal está visible
            if (modal && modal.style.display === 'flex') {
                // Si el clic fue en el fondo negro (no en el contenido)
                if (event.target === modal) {
                    closeProcessModal();
                }
            }
        });

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(event) {
            const modal = document.getElementById('viewProcessModal');
            if (event.key === 'Escape' && modal.style.display === 'flex') {
                closeProcessModal();
            }
        });

        // Helper para iconos de estado
        function getEstadoIcon(estado) {
            const icons = {
                'Pendiente': '⏳',
                'Radicado': '📋',
                'Archivado': '📁',
                'Finalizado': '✅',
                'En proceso': '🔄',
                'Reabierto': '🔓',
                'Admisión': '📝',
                'Traslado': '📤',
                'Audiencia': '👨‍⚖️',
                'Fallo favorable': '✅',
                'Fallo desfavorable': '❌',
                'Apelación': '⚖️',
                'Ejecutoria': '🔨',
                'Pago en trámite': '💰',
                'Conciliado': '🤝'
            };
            return icons[estado] || '📄';
        }

        // Renderizar documentos
        function renderDocuments(documentos) {
            if (!documentos || documentos.length === 0) {
                return `<p class="text-muted">📄 No hay documentos asociados a este proceso.</p>`;
            }

            return `
            <ul class="documents-list">
                ${documentos.map(doc => `
                                    <li>
                                        <a href="/storage/${doc.ruta}" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                            ${doc.nombre}
                                        </a>
                                    </li>
                                `).join('')}
            </ul>
        `;
        }

        // Cerrar modal
        function closeProcessModal() {
            document.getElementById('viewProcessModal').style.display = 'none';
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function(event) {
            const modal = document.getElementById('viewProcessModal');
            if (event.key === 'Escape' && modal.style.display === 'flex') {
                closeProcessModal();
            }
        });

        // Confirmar eliminación
        function confirmDelete(id, nombre) {
            Swal.fire({
                title: 'Confirmar Eliminación',
                html: `¿Estás seguro de eliminar el proceso de <b>${nombre}</b>?<br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280'
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
