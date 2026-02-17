<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- 👈 IMPORTANTE --}}
    <title>Redactar Concepto Jurídico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Enlace a CSS -->
    <link rel="stylesheet" href="{{ asset('css/createCon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
    @vite(['resources/js/dash.js'])

</head>

<body>
    <div class="dashboard-wrapper">
        <div class="overlay" id="overlay"></div>

        {{-- 👇 SIDEBAR REUTILIZABLE --}}
        <x-sidebar />

        {{-- 👇 CONTENIDO PRINCIPAL --}}
        <main class="main-content" id="mainContent">

            {{-- Header con hamburguesa y notificaciones --}}
            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <h1>Redactar Concepto Jurídico</h1>
                </div>
                <div class="header-right">
                    {{-- 👇 COMPONENTE DE NOTIFICACIONES --}}
                    <x-notification-dropdown />

                    <!-- Logo SENA -->
                    <a href="{{ route('dashboard.abogado') }}">
                        <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
                    </a>
                </div>
            </header>
            <div class="container">

                <!-- Información del Proceso -->
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-info-circle"></i>
                            Información del Proceso
                        </h5>
                        <span class="badge">{{ $proceso->estado }}</span>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-hashtag" style="color: #10b981;"></i>
                                        Número de Radicado:
                                    </div>
                                    <div class="info-value primary">{{ $proceso->numero_radicado }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-balance-scale" style="color: #10b981;"></i>
                                        Tipo de Proceso:
                                    </div>
                                    <div class="info-value">{{ $proceso->tipo_proceso }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-alt" style="color: #8b5cf6;"></i>
                                        Fecha de Radicación:
                                    </div>
                                    <div class="info-value">{{ $proceso->created_at }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user-plus" style="color: #f59e0b;"></i>
                                        Demandante:
                                    </div>
                                    <div class="info-value">{{ $proceso->demandante }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user-minus" style="color: #ef4444;"></i>
                                        Demandado:
                                    </div>
                                    <div class="info-value">{{ $proceso->demandado }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario del Concepto Jurídico -->
                <div class="card">
                    <div class="card-header primary">
                        <h5>
                            <i class="fas fa-pen-alt"></i>
                            Redactar Concepto Jurídico
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="conceptoForm" method="POST"
                            action="{{ route('abogado.conceptos.storeProceso', $proceso->id) }}">
                            @csrf
                            <!-- Título del Concepto Jurídico -->
                            <div class="form-group-modern">
                                <label for="titulo" class="form-label-modern">
                                    <i class="fas fa-heading icon-modern" style="color: #10b981;"></i>
                                    Título del Concepto <span class="required">*</span>
                                </label>

                                <div class="input-wrapper-modern">
                                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}"
                                        class="form-input-modern"
                                        placeholder="Ejemplo: Análisis de la responsabilidad civil en el caso"
                                        maxlength="120" required>
                                </div>

                                <p class="form-help-modern">
                                    Escribe un título breve y descriptivo para el concepto (máx. 120 caracteres).
                                </p>
                            </div>

                            <!-- Concepto Jurídico Principal -->
                            <div class="form-group">
                                <label for="concepto" class="form-label">
                                    <i class="fas fa-gavel" style="color: #10b981;"></i>
                                    Concepto Jurídico
                                    <span class="required">*</span>
                                </label>
                                <div class="form-help">
                                    Redacta un análisis jurídico completo y fundamentado del caso (mínimo 50
                                    caracteres).
                                </div>
                                <div class="textarea-container">
                                    <textarea id="concepto" name="concepto" rows="12" class="form-textarea" required>{{ old('concepto') }}</textarea>
                                    <div id="conceptoCounter" class="char-counter">0 caracteres</div>
                                </div>
                                <div id="conceptoError" class="form-error">
                                    El concepto debe tener al menos 50 caracteres.
                                </div>
                                @error('concepto')
                                    <div class="form-error show">{{ $message }}</div>
                                @enderror

                                @if ($errors->has('general'))
                                    <div class="form-error show">{{ $errors->first('general') }}</div>
                                @endif
                            </div>

                            <!-- Botones de Acción -->
                            <div class="btn-group">
                                <a href="{{ route('conceptos.create') }}" class="btn btn-cancel">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                                <div class="btn-actions">
                                    <button type="submit" id="submitBtn" disabled class="btn btn-submit">
                                        <i class="fas fa-check"></i>
                                        Finalizar Concepto
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panel de Ayuda -->
                <div class="help-panel">
                    <div class="help-header">
                        <h6>
                            <i class="fas fa-question-circle" style="color: #3b82f6;"></i>
                            Guía para Redactar el Concepto
                        </h6>
                    </div>
                    <div class="help-body">
                        <div class="help-grid">
                            <div class="help-section">
                                <h6>Estructura Sugerida:</h6>
                                <div class="help-list">
                                    <div class="help-item success">
                                        <i class="fas fa-check" style="color: #10b981;"></i>
                                        <span>Análisis de hechos</span>
                                    </div>
                                    <div class="help-item success">
                                        <i class="fas fa-check" style="color: #10b981;"></i>
                                        <span>Marco jurídico aplicable</span>
                                    </div>
                                    <div class="help-item success">
                                        <i class="fas fa-check" style="color: #10b981;"></i>
                                        <span>Análisis legal</span>
                                    </div>
                                    <div class="help-item success">
                                        <i class="fas fa-check" style="color: #10b981;"></i>
                                        <span>Conclusiones</span>
                                    </div>
                                </div>
                            </div>
                            <div class="help-section">
                                <h6>Consideraciones:</h6>
                                <div class="help-list">
                                    <div class="help-item warning">
                                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                                        <span>Fundamentar en normatividad vigente</span>
                                    </div>
                                    <div class="help-item warning">
                                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                                        <span>Usar lenguaje técnico apropiado</span>
                                    </div>
                                    <div class="help-item warning">
                                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                                        <span>Ser claro y preciso</span>
                                    </div>
                                    <div class="help-item warning">
                                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                                        <span>Incluir jurisprudencia si aplica</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL DE CONFIRMACIÓN -->
            <div id="confirmModal" class="modal-overlay">
                <div class="modal-confirm">
                    <div class="modal-content">
                        <div class="modal-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3 class="modal-title">¡Perfecto!</h3>
                        <p class="modal-message">
                            Una vez enviado, no podrás modificar este concepto jurídico.
                            Asegúrate de que toda la información esté correcta.
                        </p>
                    </div>
                    <div class="modal-buttons">
                        <button type="button" class="modal-btn modal-btn-cancel" onclick="closeModal()">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                        <button type="button" class="modal-btn modal-btn-confirm" onclick="confirmSubmit()">
                            <i class="fas fa-check"></i>
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
    </div>

    <script src="{{ asset('js/createCon.js') }}"></script>
</body>

</html>
