<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Pagos de Sentencias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pagos.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <!-- Título -->
                <div class="header-title">
                    <i class="fas fa-dollar-sign"></i>
                    <div>
                        <h1>Gestión de Pagos de Sentencias</h1>
                        <p class="header-subtitle">Control y registro de pagos judiciales</p>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="header-actions">
                    <a href="{{ route('mis.procesos') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>

                    <div class="total-badge">
                        <div class="label">Total procesos</div>
                        <div class="value" id="totalProcesos">{{ count($procesosData) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="label">Pagos Pendientes</div>
                        <div class="value" id="pagosPendientes">0</div>
                    </div>
                    <i class="fas fa-clock stat-icon"></i>
                </div>
            </div>

            <div class="stat-card paid">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="label">Pagos Realizados</div>
                        <div class="value" id="pagosRealizados">0</div>
                    </div>
                    <i class="fas fa-check-circle stat-icon"></i>
                </div>
            </div>

            <div class="stat-card total">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="label">Total Pagado</div>
                        <div class="value" id="totalPagado">$0</div>
                    </div>
                    <i class="fas fa-dollar-sign stat-icon"></i>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filtro="todos">Todos</button>
                <button class="filter-btn pendientes" data-filtro="pendientes">Pendientes</button>
                <button class="filter-btn pagados" data-filtro="pagados">Pagados</button>
            </div>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchRadicado" placeholder="Buscar por número de radicado...">
        </div>

        <!-- Lista de procesos -->
        <div class="procesos-list" id="procesosList"></div>

        <!-- Contenedor de paginación -->
        <div class="pagination-desktop" style="display:flex; justify-content:center; gap:0.3rem; margin-top:1rem;">
        </div>

        <!-- Estado vacío -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="fas fa-exclamation-circle"></i>
            <h3>No hay procesos en esta categoría</h3>
            <p>Intenta cambiar los filtros para ver más resultados</p>
        </div>
    </div>

    <!-- Modal: Registrar Pago -->
    <div class="modal" id="modalPago">
        <div class="modal-content">
            <div class="modal-header">
                <h2>
                    <i class="fas fa-dollar-sign"></i>
                    Registrar Pago de Sentencia
                </h2>
                <button class="btn-close" onclick="cerrarModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="proceso-info-box" id="procesoInfoModal"></div>

                <div class="form-group">
                    <label>Valor del Pago (COP) *</label>
                    <input type="text" id="valorSentencia" name="valor_pagado" placeholder="Ej: 15.000.000"
                        class="form-control">
                    <small id="maxValorHint" style="color: #64748b;"></small>
                </div>

                <div class="form-group">
                    <label>Forma de Pago *</label>
                    <select id="formaPago">
                        <option>Efectivo</option>
                        <option selected>Transferencia</option>
                        <option>Consignación</option>
                        <option>Tarjeta</option>
                        <option>Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha de Pago *</label>
                    <input type="date" id="fechaPago">
                </div>

                <div class="form-group">
                    <label>Comprobante de Pago (PDF / Imagen) *</label>
                    <input type="file" id="comprobantePago" accept=".pdf,image/*">
                    <small>Formatos permitidos: PDF, JPG, PNG (máx 5MB)</small>
                </div>

                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea id="observaciones" rows="4" placeholder="Detalles adicionales sobre el pago..."></textarea>
                </div>

                <div class="modal-actions">
                    <button class="btn-primary" onclick="registrarPago()">
                        <i class="fas fa-check-circle"></i>
                        Registrar Pago
                    </button>
                    <button class="btn-secondary" onclick="cerrarModal()">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let paginaActual = 1;
        const itemsPorPagina = 10;
        let procesos = @json($procesosData);
        let filtroActual = 'todos';
        let procesoSeleccionado = null;
        let terminoBusqueda = '';
        const esAbogado = @json($esAbogado); // 👈 AGREGAR ESTA LÍNEA

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function formatearPeso(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(valor || 0);
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-CO');
        }

        function actualizarEstadisticas() {
            const pendientes = procesos.filter(p => p.requiere_pago && p.porcentaje < 100).length;
            const realizados = procesos.filter(p => p.porcentaje >= 100).length;
            const totalPagado = procesos.reduce((sum, p) => sum + (p.total_pagado || 0), 0);

            document.getElementById('totalProcesos').textContent = procesos.length;
            document.getElementById('pagosPendientes').textContent = pendientes;
            document.getElementById('pagosRealizados').textContent = realizados;
            document.getElementById('totalPagado').textContent = formatearPeso(totalPagado);
        }

        function renderizarProcesos() {

            // 👇 ORDENAR POR ID ASCENDENTE
            procesos.sort((a, b) => a.id - b.id);
            const lista = document.getElementById('procesosList');
            const emptyState = document.getElementById('emptyState');
            let procesosFiltrados = procesos.filter(p => {
                if (filtroActual === 'pendientes' && (!p.requiere_pago || p.porcentaje >= 100)) {
                    return false;
                }
                if (filtroActual === 'pagados' && p.porcentaje < 100) {
                    return false;
                }
                if (terminoBusqueda) {
                    return p.radicado.toString().toLowerCase().includes(terminoBusqueda.toLowerCase());
                }
                return true;
            });

            if (procesosFiltrados.length === 0) {
                lista.style.display = 'none';
                emptyState.style.display = 'block';
                document.querySelector('.pagination-desktop').innerHTML = '';
                return;
            }

            lista.style.display = 'grid';
            emptyState.style.display = 'none';

            const totalItems = procesosFiltrados.length;
            const inicio = (paginaActual - 1) * itemsPorPagina;
            const fin = inicio + itemsPorPagina;
            procesosFiltrados = procesosFiltrados.slice(inicio, fin);

            lista.innerHTML = procesosFiltrados.map(proceso => `
            <div class="proceso-card">
                <div class="proceso-header" style="display:flex; justify-content:space-between; gap:1rem;">
                    <div class="proceso-info">
                        <h3>${proceso.nombre}</h3>
                        <p><strong>Radicado:</strong> ${proceso.radicado}</p>
                        <p><strong>Demandante:</strong> ${proceso.demandante}</p>
                    </div>
                    <div>
                        ${!proceso.requiere_pago
                            ? `<span class="status-badge no-requiere">No requiere pago</span>`
                            : proceso.porcentaje >= 100
                                ? `<span class="status-badge pagado"><i class="fas fa-check-circle"></i> Pago completado</span>`
                                : `<span class="status-badge pendiente"><i class="fas fa-clock"></i> Pago en trámite</span>`
                        }
                    </div>
                </div>

                ${proceso.requiere_pago ? `
                                                            <div class="pago-section">
                                                                <div class="pago-realizado">
                                                                    <div class="pago-item">
                                                                        <div class="label">Valor Estimado</div>
                                                                        <div class="value">${formatearPeso(proceso.valor_estimado)}</div>
                                                                    </div>
                                                                    <div class="pago-item">
                                                                        <div class="label">Total Pagado</div>
                                                                        <div class="value">${formatearPeso(proceso.total_pagado)}</div>
                                                                    </div>
                                                                    <div class="pago-item">
                                                                        <div class="label">Falta por Pagar</div>
                                                                        <div class="value">${formatearPeso(proceso.falta_pagar)}</div>
                                                                    </div>
                                                                    <div class="pago-item">
                                                                        <div class="label">Progreso</div>
                                                                        <div class="value">${proceso.porcentaje}%</div>
                                                                    </div>
                                                                </div>

                                                                ${proceso.porcentaje > 0 ? `
                            <div style="margin: 1rem 0;">
                                <div style="background: #e2e8f0; border-radius: 999px; height: 12px; overflow: hidden;">
                                    <div style="background: linear-gradient(90deg, #16a34a, #22c55e); height: 100%; width: ${proceso.porcentaje}%; transition: width 0.3s ease;"></div>
                                </div>
                            </div>
                        ` : ''}

                                                                ${proceso.cuotas && proceso.cuotas.length > 0 ? `
                            <div class="historial-pagos">
                                <button class="btn-toggle-historial" onclick="toggleHistorial(${proceso.id})">
                                    <i class="fas fa-chevron-down"></i>
                                    Ver historial de pagos (${proceso.cuotas.length})
                                </button>
                                <div class="historial-contenido" id="historial-${proceso.id}" style="display:none;">
${proceso.cuotas.map((pago, index) => `
                                    <div style="margin-top: 0.5rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px; border-left: 3px solid #16a34a;">
                                        
                                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                            <div>
                                                <div style="font-weight:600;">
                                                    Pago #${index + 1} - ${formatearPeso(pago.valor_pagado)}
                                                </div>
                                                <div style="font-size:0.85rem; color:#64748b;">
                                                    <i class="fas fa-calendar"></i> ${formatearFecha(pago.fecha_pago)}
                                                </div>
                                            </div>

                                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                                
                                                ${pago.pago_estado === 'Pendiente'
                                                    ? `<span style="background:#facc15; color:#000; padding:4px 8px; border-radius:6px; font-size:0.8rem;">
                        <i class="fas fa-clock"></i> Pendiente
                       </span>`
                                                    : pago.pago_estado === 'Aprobado'
                                                        ? `<span style="background:#16a34a; color:white; padding:4px 8px; border-radius:6px; font-size:0.8rem;">
                            <i class="fas fa-check-circle"></i> Aprobado
                           </span>`
                                                        : pago.pago_estado === 'Rechazado'
                                                            ? `<span style="background:#dc2626; color:white; padding:4px 8px; border-radius:6px; font-size:0.8rem;">
                                <i class="fas fa-times-circle"></i> Rechazado
                               </span>`
                                                            : ''
                                                }

                                                ${pago.comprobante ? `
                    <a href="/storage/${pago.comprobante}" target="_blank"
                        style="padding: 0.4rem 0.75rem; background: white; border: 1px solid #e2e8f0;
                               border-radius: 6px; color: #3b82f6; text-decoration: none;
                               font-size: 0.8rem; display: flex; align-items: center;
                               gap: 0.4rem; white-space: nowrap;">
                        <i class="fas fa-file-pdf"></i> Ver comprobante
                    </a>
                ` : ''}
                                            </div>
                                        </div>

                                        ${pago.observaciones ? `
            <div style="margin-top:0.5rem; padding:0.5rem; background:white;
                        border-radius:6px; border-left:2px solid #3b82f6; font-size:0.85rem;">
                <strong>Observaciones:</strong>
                <div>${pago.observaciones}</div>
            </div>
        ` : ''}

                                        ${pago.pago_estado === 'Rechazado' && pago.motivo_rechazo ? `
            <div style="margin-top:0.5rem; padding:0.5rem; background:#fee2e2;
                        border-radius:6px; border-left:2px solid #dc2626; font-size:0.85rem;">
                <strong>Motivo del rechazo:</strong>
                <div>${pago.motivo_rechazo}</div>
            </div>
        ` : ''}

                        ${pago.pago_estado === 'Pendiente' && esAbogado ? `
                <div style="margin-top:0.75rem; display:flex; gap:0.5rem;">
                    <button onclick="aprobarPago(${pago.pago_id})"
                            style="background:#16a34a; color:white; border:none;
                                padding:6px 10px; border-radius:6px; cursor:pointer;">
                        <i class="fas fa-check"></i> Aprobar
                    </button>

                    <button onclick="rechazarPago(${pago.pago_id})"
                            style="background:#dc2626; color:white; border:none;
                                padding:6px 10px; border-radius:6px; cursor:pointer;">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </div>
            ` : ''}

                                    </div>
                                `).join('')}

                                </div>
                            </div>
                        ` : ''}

                                                                ${proceso.porcentaje < 100 ? `
                            <button class="btn-registrar" onclick="abrirModal(${proceso.id})">
                                <i class="fas fa-dollar-sign"></i>
                                Registrar nuevo pago
                            </button>
                        ` : ''}
                                                            </div>
                                                        ` : ''}
            </div>
        `).join('');

            renderizarPaginacion(totalItems);
        }

        function toggleHistorial(id) {
            const historial = document.getElementById(`historial-${id}`);
            const btn = historial.previousElementSibling;
            const icon = btn.querySelector('i');

            if (historial.style.display === 'none') {
                historial.style.display = 'block';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                historial.style.display = 'none';
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }

        // Abrir modal
        function abrirModal(id) {
            procesoSeleccionado = procesos.find(p => p.id === id);

            document.getElementById('procesoInfoModal').innerHTML = `
            <h3>${procesoSeleccionado.nombre}</h3>
            <p><strong>Radicado:</strong> ${procesoSeleccionado.radicado}</p>
            <p><strong>Demandante:</strong> ${procesoSeleccionado.demandante}</p>
            <div style="margin-top: 0.75rem; padding: 0.75rem; background: #fef2f2; border-left: 3px solid #dc2626; border-radius: 6px;">
                <div style="color: #dc2626; font-weight: 600;">
                    <i class="fas fa-exclamation-circle"></i>
                    Falta por pagar: ${formatearPeso(procesoSeleccionado.falta_pagar)}
                </div>
            </div>
        `;

            document.getElementById('valorSentencia').max = procesoSeleccionado.falta_pagar;
            document.getElementById('valorSentencia').value = '';
            document.getElementById('maxValorHint').textContent =
                `Máximo permitido: ${formatearPeso(procesoSeleccionado.falta_pagar)}`;

            document.getElementById('formaPago').value = 'Transferencia';
            document.getElementById('fechaPago').value = new Date().toISOString().split('T')[0];
            document.getElementById('observaciones').value = '';
            document.getElementById('comprobantePago').value = '';

            document.getElementById('modalPago').classList.add('show');
        }

        function cerrarModal() {
            document.getElementById('modalPago').classList.remove('show');
            procesoSeleccionado = null;
        }

        async function registrarPago() {
            const valorRaw = document.getElementById('valorSentencia').value;
            const valor = parseInt(valorRaw.replace(/\D/g, ''), 10);

            const forma = document.getElementById('formaPago').value;
            const fecha = document.getElementById('fechaPago').value;
            const obs = document.getElementById('observaciones').value;
            const archivo = document.getElementById('comprobantePago').files[0];

            if (!valor || !fecha) {
                Swal.fire('Error', 'Completa los campos obligatorios', 'warning');
                return;
            }

            if (!archivo) {
                Swal.fire('Error', 'Debes subir el comprobante de pago', 'warning');
                return;
            }

            if (valor > procesoSeleccionado.falta_pagar) {
                Swal.fire(
                    'Error',
                    `El valor no puede exceder ${formatearPeso(procesoSeleccionado.falta_pagar)}`,
                    'warning'
                );
                return;
            }

            // 👇 VERIFICAR SI ES EL ÚLTIMO PAGO
            const esUltimoPago = valor >= procesoSeleccionado.falta_pagar;

            // 👇 MENSAJE DE CONFIRMACIÓN (ANTES DE REGISTRAR)
            const confirmacion = await Swal.fire({
                title: '¿Confirmar pago?',
                html: `
            <p>Valor a registrar: <strong>${formatearPeso(valor)}</strong></p>
            ${esAbogado 
                ? (esUltimoPago
                    ? '<p style="color: #dc2626; font-weight: 600;">⚠️ <strong>IMPORTANTE:</strong> Este pago completará el total y el proceso será <strong>ARCHIVADO AUTOMÁTICAMENTE</strong>.</p>'
                    : '<p style="color: #16a34a;">✅ Este pago se aprobará automáticamente.</p>')
                : (esUltimoPago
                    ? '<p style="color: #dc2626; font-weight: 600;">⚠️ <strong>IMPORTANTE:</strong> El pago quedará en revisión. Una vez aprobado por el abogado, <strong>el proceso será ARCHIVADO automáticamente y no podria ser modificado</strong>.</p>'
                    : '<p style="color: #f59e0b;">⏳ El pago quedará en <strong>REVISIÓN</strong> y deberá ser aprobado por el abogado.</p>')
            }
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#dc2626'
            });

            if (!confirmacion.isConfirmed) return;

            const formData = new FormData();
            formData.append('proceso_id', procesoSeleccionado.id);
            formData.append('valor_pagado', valor);
            formData.append('forma_pago', forma);
            formData.append('fecha_pago', fecha);
            formData.append('observaciones', obs);
            formData.append('comprobante', archivo);

            try {
                const response = await fetch('/pagos', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // 👇 MENSAJE DE ÉXITO (DESPUÉS DE REGISTRAR) - MÁS SIMPLE
                    Swal.fire({
                        icon: 'success',
                        title: esAbogado ? '¡Pago Aprobado!' : '¡Pago Registrado!',
                        html: esAbogado ?
                            'El pago ha sido aprobado automáticamente.' :
                            '<p>El pago ha sido registrado exitosamente.</p><p style="color:#f59e0b; font-weight:600;">📋 Estado: En revisión - Pendiente de aprobación por el abogado.</p>',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#16a34a',
                        allowOutsideClick: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filtroActual = this.dataset.filtro;
                paginaActual = 1;
                renderizarProcesos();
            });
        });

        document.getElementById('searchRadicado').addEventListener('input', function() {
            terminoBusqueda = this.value.trim();
            paginaActual = 1;
            renderizarProcesos();
        });

        document.getElementById('modalPago').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });

        document.getElementById('valorSentencia').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });

        // Inicializar
        actualizarEstadisticas();
        renderizarProcesos();

        // -------------------------------------------------
        // PAGINACIÓN ESTILO BLADE
        // -------------------------------------------------
        function renderizarPaginacion(totalItems) {
            const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
            const pagContainer = document.querySelector('.pagination-desktop');
            if (!pagContainer) return;

            pagContainer.innerHTML = ''; // limpiar

            // ANTERIOR
            if (paginaActual === 1) {
                const spanPrev = document.createElement('span');
                spanPrev.className = 'pagination-btn disabled';
                spanPrev.textContent = 'Anterior';
                pagContainer.appendChild(spanPrev);
            } else {
                const aPrev = document.createElement('a');
                aPrev.className = 'pagination-btn ajax-page';
                aPrev.href = '#';
                aPrev.textContent = 'Anterior';
                aPrev.addEventListener('click', e => {
                    e.preventDefault();
                    paginaActual--;
                    renderizarProcesos();
                });
                pagContainer.appendChild(aPrev);
            }

            const start = Math.max(1, paginaActual - 2);
            const end = Math.min(totalPaginas, paginaActual + 2);

            // PRIMERA PÁGINA + "..."
            if (start > 1) {
                const first = document.createElement('a');
                first.className = 'pagination-btn ajax-page';
                first.href = '#';
                first.textContent = '1';
                first.addEventListener('click', e => {
                    e.preventDefault();
                    paginaActual = 1;
                    renderizarProcesos();
                });
                pagContainer.appendChild(first);

                if (start > 2) {
                    const dots = document.createElement('span');
                    dots.className = 'pagination-btn disabled';
                    dots.textContent = '...';
                    pagContainer.appendChild(dots);
                }
            }

            // BOTONES DEL RANGO
            for (let i = start; i <= end; i++) {
                if (i === paginaActual) {
                    const spanActive = document.createElement('span');
                    spanActive.className = 'pagination-btn active';
                    spanActive.textContent = i;
                    pagContainer.appendChild(spanActive);
                } else {
                    const aPage = document.createElement('a');
                    aPage.className = 'pagination-btn ajax-page';
                    aPage.href = '#';
                    aPage.textContent = i;
                    aPage.addEventListener('click', e => {
                        e.preventDefault();
                        paginaActual = i;
                        renderizarProcesos();
                    });
                    pagContainer.appendChild(aPage);
                }
            }

            // ÚLTIMA PÁGINA + "..."
            if (end < totalPaginas) {
                if (end < totalPaginas - 1) {
                    const dots = document.createElement('span');
                    dots.className = 'pagination-btn disabled';
                    dots.textContent = '...';
                    pagContainer.appendChild(dots);
                }

                const last = document.createElement('a');
                last.className = 'pagination-btn ajax-page';
                last.href = '#';
                last.textContent = totalPaginas;
                last.addEventListener('click', e => {
                    e.preventDefault();
                    paginaActual = totalPaginas;
                    renderizarProcesos();
                });
                pagContainer.appendChild(last);
            }

            // SIGUIENTE
            if (paginaActual === totalPaginas || totalPaginas === 0) {
                const spanNext = document.createElement('span');
                spanNext.className = 'pagination-btn disabled';
                spanNext.textContent = 'Siguiente';
                pagContainer.appendChild(spanNext);
            } else {
                const aNext = document.createElement('a');
                aNext.className = 'pagination-btn ajax-page';
                aNext.href = '#';
                aNext.textContent = 'Siguiente';
                aNext.addEventListener('click', e => {
                    e.preventDefault();
                    paginaActual++;
                    renderizarProcesos();
                });
                pagContainer.appendChild(aNext);
            }
        }

        async function aprobarPago(id) {
            const confirmacion = await Swal.fire({
                title: '¿Aprobar este pago?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#16a34a'
            });

            if (!confirmacion.isConfirmed) return;

            const response = await fetch(`/pagos/${id}/aprobar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire('Éxito', data.message, 'success')
                    .then(() => location.reload());
            }
        }

        async function rechazarPago(id) {
            const {
                value: motivo
            } = await Swal.fire({
                title: 'Motivo del rechazo',
                input: 'textarea',
                inputLabel: 'Debes escribir el motivo',
                inputPlaceholder: 'Escribe el motivo del rechazo...',
                showCancelButton: true,
                confirmButtonText: 'Rechazar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                inputValidator: (value) => {
                    if (!value || value.length < 5) {
                        return 'El motivo es obligatorio (mínimo 5 caracteres)';
                    }
                }
            });

            if (!motivo) return;

            const response = await fetch(`/pagos/${id}/rechazar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    motivo
                })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire('Rechazado', data.message, 'success')
                    .then(() => location.reload());
            }
        }
    </script>

</body>

</html>
