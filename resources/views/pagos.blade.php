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
    <script src="{{ asset('js/pagos.js') }}" defer></script>

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
                    <a href="#" class="back-btn" onclick="window.history.back()">
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

        <!-- Lista de procesos -->
        <div class="procesos-list" id="procesosList"></div>

        <!-- Estado vacío -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="fas fa-exclamation-circle"></i>
            <h3>No hay procesos en esta categoría</h3>
            <p>Intenta cambiar los filtros para ver más resultados</p>
        </div>
    </div>

    <!-- Modal -->
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
                    <label>Valor de la Sentencia (COP) *</label>
                    <input type="number" id="valorSentencia" placeholder="15000000">
                </div>

                <div class="form-group">
                    <label>Forma de Pago *</label>
                    <select id="formaPago">
                        <option>Efectivo</option>
                        <option>Transferencia</option>
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
                    <label>Comprobante de Pago (PDF / Imagen)</label>
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
        // 🔥 DATOS DESDE LARAVEL
        let procesos = @json($procesosData);
        let filtroActual = 'todos';
        let procesoSeleccionado = null;

        // Token CSRF para peticiones AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Formatear peso colombiano
        function formatearPeso(valor) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0
            }).format(valor);
        }

        // Formatear fecha
        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-CO');
        }

        // Actualizar estadísticas
        function actualizarEstadisticas() {
            const pendientes = procesos.filter(p => p.requiere_pago && !p.pago_realizado).length;
            const realizados = procesos.filter(p => p.pago_realizado).length;
            const totalPagado = procesos
                .filter(p => p.pago_realizado)
                .reduce((sum, p) => sum + (parseFloat(p.valor_sentencia) || 0), 0);

            document.getElementById('totalProcesos').textContent = procesos.length;
            document.getElementById('pagosPendientes').textContent = pendientes;
            document.getElementById('pagosRealizados').textContent = realizados;
            document.getElementById('totalPagado').textContent = formatearPeso(totalPagado);
        }

        // Renderizar procesos (MISMO CÓDIGO ANTERIOR)
        function renderizarProcesos() {
            const lista = document.getElementById('procesosList');
            const emptyState = document.getElementById('emptyState');

            let procesosFiltrados = procesos;

            if (filtroActual === 'pendientes') {
                procesosFiltrados = procesos.filter(p => p.requiere_pago && !p.pago_realizado);
            } else if (filtroActual === 'pagados') {
                procesosFiltrados = procesos.filter(p => p.pago_realizado);
            }

            if (procesosFiltrados.length === 0) {
                lista.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            lista.style.display = 'grid';
            emptyState.style.display = 'none';

            lista.innerHTML = procesosFiltrados.map(proceso => `
                <div class="proceso-card">
                    <div class="proceso-header">
                        <div class="proceso-info">
                            <h3>${proceso.nombre}</h3>
                            <div class="proceso-details">
                                <div class="detail-item">
                                    <i class="fas fa-file-alt"></i>
                                    <span><strong>Radicado:</strong> ${proceso.radicado}</span>
                                </div>
                                <div class="detail-item">
                                    <span><strong>Demandante:</strong> ${proceso.demandante}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            ${proceso.requiere_pago ? (
                                proceso.pago_realizado 
                                    ? `<span class="status-badge pagado">
                                                                            <i class="fas fa-check-circle"></i>
                                                                            Pago Realizado
                                                                           </span>`
                                    : `<span class="status-badge pendiente">
                                                                            <i class="fas fa-clock"></i>
                                                                            Pago Pendiente
                                                                           </span>`
                            ) : `<span class="status-badge no-requiere">No requiere pago</span>`}
                        </div>
                    </div>
                    
                    ${proceso.requiere_pago ? `
                                                            <div class="pago-section">
                                                                ${proceso.pago_realizado ? `
                                <div class="pago-realizado">
                                    <div class="pago-item destacado">
                                        <div class="label">Valor Pagado</div>
                                        <div class="value">${formatearPeso(proceso.valor_sentencia)}</div>
                                    </div>
                                    <div class="pago-item">
                                        <div class="label">Forma de Pago</div>
                                        <div class="value">${proceso.forma_pago}</div>
                                    </div>
                                    <div class="pago-item">
                                        <div class="label">Fecha de Pago</div>
                                        <div class="value">
                                            <i class="fas fa-calendar"></i>
                                            ${formatearFecha(proceso.fecha_pago)}
                                        </div>
                                    </div>
                              ${proceso.observaciones ? `
                <div class="pago-item" style="grid-column: 1 / -1;">
                    <div class="label">Observaciones</div>
                    <div class="value">${proceso.observaciones}</div>
                </div>
            ` : ''}

${proceso.comprobante ? `
                <div class="pago-item" style="grid-column: 1 / -1;">
                    <div class="label">Comprobante de Pago</div>
                    <a href="/storage/${proceso.comprobante}" 
                       target="_blank" 
                       class="link-comprobante">
                        <i class="fas fa-paperclip"></i> Ver comprobante
                    </a>
                </div>
            ` : ''}

                                </div>
                            ` : `
                                <div class="pago-pendiente">
                                    <div class="pago-item destacado">
                                        <div class="label">Valor Estimado</div>
                                        <div class="value">${formatearPeso(proceso.valor_estimado)}</div>
                                    </div>
                                    <button class="btn-registrar" onclick="abrirModal(${proceso.id})">
                                        <i class="fas fa-dollar-sign"></i>
                                        Registrar Pago
                                    </button>
                                </div>
                            `}
                                                            </div>
                                                        ` : ''}
                </div>
            `).join('');
        }

        // Cambiar filtro
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filtroActual = this.dataset.filtro;
                renderizarProcesos();
            });
        });

        // Abrir modal
        function abrirModal(id) {
            procesoSeleccionado = procesos.find(p => p.id === id);

            document.getElementById('procesoInfoModal').innerHTML = `
                <h3>${procesoSeleccionado.nombre}</h3>
                <p><strong>Radicado:</strong> ${procesoSeleccionado.radicado}</p>
                <p><strong>Demandante:</strong> ${procesoSeleccionado.demandante}</p>
            `;

            document.getElementById('valorSentencia').value = procesoSeleccionado.valor_estimado || '';
            document.getElementById('formaPago').value = 'Transferencia';
            document.getElementById('fechaPago').value = new Date().toISOString().split('T')[0];
            document.getElementById('observaciones').value = '';

            document.getElementById('modalPago').classList.add('show');
        }

        // Cerrar modal
        function cerrarModal() {
            document.getElementById('modalPago').classList.remove('show');
            procesoSeleccionado = null;
        }

        // 🔥 REGISTRAR PAGO CON LARAVEL
        async function registrarPago() {
            const valor = document.getElementById('valorSentencia').value;
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
                    Swal.fire('Éxito', data.message, 'success');

                    procesos = procesos.map(p =>
                        p.id === procesoSeleccionado.id ? {
                            ...p,
                            pago_realizado: true,
                            valor_sentencia: parseFloat(valor),
                            forma_pago: forma,
                            fecha_pago: fecha,
                            observaciones: obs
                        } : p
                    );

                    cerrarModal();
                    actualizarEstadisticas();
                    renderizarProcesos();
                } else {
                    Swal.fire('Error', 'No se pudo registrar el pago', 'error');
                }

            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalPago').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });

        // Inicializar
        actualizarEstadisticas();
        renderizarProcesos();
    </script>
</body>

</html>
