<!DOCTYPE html>
<html lang="es">

<head><!-- pagina para ver que procesos tiene pendiente para concepto  -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesos Pendientes - CSS Puro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Enlace a CSS corregido -->
    <link rel="stylesheet" href="{{ asset('css/showCon.css') }}">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fas fa-balance-scale"></i>
                <span>Sistema Jurídico</span>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1>Procesos Pendientes de Concepto Jurídico</h1>
                <p>Gestiona los procesos que requieren análisis jurídico</p>
            </div>
            <!-- busqueda -->
            <!-- Buscador moderno -->
            <div class="search-wrapper">
                <div class="search-group">
                    <input type="text" id="searchInput" class="search-input-modern"
                        placeholder="Buscar por ID, radicado o fecha...">
                    <button id="searchBtn" class="search-button-modern">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

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
        <div class="process-grid">
            @include('profile.partials.process-card', ['proceso' => $procesos])
        </div>

        <!-- Recordatorio -->
        <div class="reminder">
            <div class="reminder-content">
                <div class="reminder-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="reminder-text">
                    <h4>Recordatorio Importante</h4>
                    <p>Los conceptos jurídicos deben ser claros, precisos y fundamentados en la normatividad vigente.
                        Asegúrate de incluir todas las referencias legales pertinentes y un análisis detallado del caso.
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

    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            text-align: right;
        }
    </style>

    <script>
        function closeAlert(alertId) {
            document.getElementById(alertId).classList.add('hidden');
        }

        // Ejemplo para mostrar alerta de éxito
        function showSuccessAlert() {
            document.getElementById('success-alert').classList.remove('hidden');
        }

        // ===== FUNCIONALIDAD DE BÚSQUEDA AJAX =====
        let searchTimeout;

        function performSearch(searchTerm) {
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);
            params.append('ajax', '1');

            // Usar la ruta actual (o reemplaza por route('procesos.index'))
            fetch(`${window.location.pathname}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.html) {
                        // Reemplaza el grid de tarjetas con el HTML devuelto
                        const grid = document.querySelector('.process-grid');
                        if (grid) {
                            grid.innerHTML = data.html;
                        }

                        // Si el backend devuelve un total, actualiza el contador si existe
                        if (data.total !== undefined) {
                            const totalEl = document.getElementById('totalCount');
                            if (totalEl) totalEl.textContent = data.total;
                        }

                        // Actualizar URL sin recargar
                        const newUrl = new URL(window.location);
                        if (searchTerm) newUrl.searchParams.set('search', searchTerm);
                        else newUrl.searchParams.delete('search');
                        newUrl.searchParams.delete('page');
                        window.history.replaceState({}, '', newUrl.toString());
                    } else {
                        console.error('Respuesta inválida de búsqueda', data);
                    }
                })
                .catch(err => console.error('Error en búsqueda:', err));
        }

        // ===== ABRIR Y CERRAR MODAL DE PROCESO =====
        function openProcessModal(id) {
            document.getElementById('viewProcessModal').style.display = 'flex';
            const body = document.getElementById('processModalBody');
            body.innerHTML = '<p>Cargando datos...</p>';

            fetch(`/concepto_juridicos/${id}`)
                .then(res => {
                    if (!res.ok) throw new Error('No se pudo obtener el concepto');
                    return res.json();
                })
                .then(data => {
                    // Ajusta las propiedades según tu modelo (titulo, descripcion, proceso, abogado...)
                    const html = `
                <p><strong>Título:</strong> ${data.titulo ?? '—'}</p>
                <p><strong>Descripción:</strong> ${data.descripcion ?? data.concepto ?? '—'}</p>
                <p><strong>Proceso:</strong> ${data.proceso?.numero_radicado ?? '—'}</p>
                <p><strong>Abogado:</strong> ${data.abogado?.name ?? '—'}</p>
            `;
                    body.innerHTML = html;
                })
                .catch(() => {
                    body.innerHTML = '<p>Error al cargar los datos.</p>';
                });
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

</body>

</html>
