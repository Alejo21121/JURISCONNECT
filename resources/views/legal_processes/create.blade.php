<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/js/dash.js'])
    <title>Crear Proceso Judicial</title>
    <!-- Enlace a CSS corregido -->
    <link rel="stylesheet" href="{{ asset('css/createPro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
</head>

<body>

    <!-- Overlay de alertas personalizadas -->
    <div id="alertOverlay" class="alert-overlay">
        <div id="customAlert" class="custom-alert">
            <div id="alertIcon" class="alert-icon"></div>
            <h2 id="alertTitle" class="alert-title"></h2>
            <p id="alertMessage" class="alert-message"></p>
            <div id="alertButtons" class="alert-buttons"></div>
        </div>
    </div>

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
                    <h1> Crear Nuevo Proceso Judicial</h1>
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
                <div class="form-wrapper">
                    <!-- Header -->
                    <div class="header">
                        <form id="formProceso" action="{{ route('procesos.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <h1>
                                <svg class="header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Complete los datos del proceso judicial
                            </h1>
                    </div>
                    <div class="form-content">
                        <!-- Mensajes de error -->
                        <div class="error-container" id="errorContainer" style="display: none;">
                            <div class="error-header">
                                <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="error-title">Se encontraron errores:</h3>
                            </div>
                            <ul class="error-list">
                                <li>Ejemplo de error de validación</li>
                            </ul>
                        </div>

                        <!-- Grid de campos principales -->
                        <div class="form-grid">
                            <!-- Tipo de Proceso -->
                            <div class="field-group">
                                <label for="tipo_proceso" class="field-label">
                                    <span class="label-content">
                                        <svg class="label-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        Tipo de proceso
                                    </span>
                                </label>
                                <select name="tipo_proceso" id="tipo_proceso" class="form-select">
                                    <option value="">-- Seleccione un tipo --</option>
                                    <option value="Civil">Civil</option>
                                    <option value="Penal">Penal</option>
                                    <option value="Laboral">Laboral</option>
                                    <option value="Familia">Familia</option>
                                </select>
                            </div>
                            <!-- Número de radicado -->
                            <div class="field-group">
                                <label for="numero_radicado" class="field-label">
                                    <span class="label-content">
                                        <svg class="label-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                        </svg>
                                        Número de radicado
                                    </span>
                                </label>
                                <input type="text" name="numero_radicado" id="numero_radicado" class="form-input"
                                    placeholder="11001-2025-00001">
                                @error('numero_radicado')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Partes del proceso -->
                        <div class="parties-section">
                            <h3 class="section-title">Partes del Proceso</h3>

                            <div class="parties-grid">
                                <!-- Demandante -->
                                <div class="field-group">
                                    <label for="demandante" class="field-label">
                                        <span class="label-content">
                                            <svg class="label-icon demandante-icon" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            Demandante
                                        </span>
                                    </label>
                                    <input type="text" name="demandante" id="demandante"
                                        class="form-input demandante-input"
                                        placeholder="Nombre completo del demandante">
                                </div>
                                <!-- Demandado -->
                                <div class="field-group">
                                    <label for="demandado" class="field-label">
                                        <span class="label-content">
                                            <svg class="label-icon demandado-icon" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            Demandado
                                        </span>
                                    </label>
                                    <input type="text" name="demandado" id="demandado"
                                        class="form-input demandado-input"
                                        placeholder="Nombre completo del demandado">
                                </div>

                                <div class="field-group pago-group">
                                    <label class="form-label fw-bold">¿El proceso requiere pago?</label>
                                    <select name="requiere_pago" id="requiere_pago" class="form-select" required>
                                        <option value="">Seleccione</option>
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                </div>

                                <div class="field-group pago-group d-none" id="valorEstimadoDiv">
                                    <label class="form-label fw-bold">Valor estimado del proceso</label>
                                    <input type="text" name="valor_estimado" id="valor_estimado"
                                        class="form-control" placeholder="Ej: 1.500.000">
                                </div>

                                <div class="field-group">
                                    <label for="fecha_vencimiento" class="field-label">
                                        <span class="label-content">
                                            <svg class="label-icon" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            Fecha de vencimiento (Probable)
                                        </span>
                                    </label>
                                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                        class="form-input" min="{{ date('Y-m-d') }}"
                                        value="{{ old('fecha_vencimiento') }}">
                                    <p class="help-text">
                                        Si estableces una fecha, recibirás notificaciones de recordatorio 7, 3 y 1 día
                                        antes
                                        del
                                        vencimiento.
                                    </p>
                                </div>

                            </div>
                        </div>
                        <!--  -->
                        <div class="field-group">
                            <label for="descripcion" class="field-label">
                                <span class="label-content">
                                    <svg class="label-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Detalle del caso
                                </span>
                            </label>
                            <textarea name="descripcion" id="descripcion" class="form-textarea"
                                placeholder="Describa detalladamente el caso, los hechos relevantes y las pretensiones..."></textarea>
                            <p class="help-text">Proporcione una clara y detallada del proceso judicial</p>
                        </div>
                        <!-- Documentos -->
                        <div class="document-section">
                            <div class="document-header">
                                <svg class="document-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a2 2 0 000-2.828L15.172 7z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7V5a2 2 0 00-2-2H8a2 2 0 00-2 2v2"></path>
                                </svg>
                                <h3 class="document-title">Adjuntar Documento</h3>
                            </div>

                            <div class="field-group">
                                <label for="documento" class="field-label" style="color: #15803d;">

                                    Documento inicial del proceso
                                </label>
                                <div class="file-input">
                                    <input type="file" name="documentos[]" id="documento" multiple>
                                </div>

                                <div id="filePreview" class="file-preview"></div>
                                <p class="file-help">Formatos permitidos: PDF, DOC, DOCX. Tamaño máximo: 10MB</p>
                            </div>
                        </div>
                        <!-- Botones -->
                        <div class="button-container">
                            <a href="javascript:void(0)" class="btn btn-cancel" onclick="window.history.back()">
                                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crear Proceso Judicial
                            </button>
                        </div>
                        </form>

                    </div>
                </div>
            </div>
    </div>

    <script>
        window.PROCESOS_CONFIG = {
            storeUrl: "{{ route('procesos.store') }}",
            indexUrl: "{{ route('procesos.index') }}",
            csrf: "{{ csrf_token() }}"
        };

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

    <script src="{{ asset('js/create.js') }}"></script>
</body>

</html>
