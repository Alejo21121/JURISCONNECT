<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redactar Concepto Jurídico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Enlace a CSS -->
    <link rel="stylesheet" href="{{ asset('css/createCon.css') }}">

    <style>
        /* ESTILOS DEL MODAL DE CONFIRMACIÓN */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-confirm {
            background: white;
            border-radius: 16px;
            padding: 0;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
            overflow: hidden;
        }

        .modal-content {
            padding: 40px 32px 32px;
            text-align: center;
        }

        .modal-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.4s ease-out 0.1s both;
        }

        .modal-icon i {
            color: white;
            font-size: 36px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 12px;
        }

        .modal-message {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
            margin: 0;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            padding: 0 32px 32px;
        }

        .modal-btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .modal-btn-cancel {
            background: #f3f4f6;
            color: #6b7280;
        }

        .modal-btn-cancel:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .modal-btn-confirm {
            background: #10b981;
            color: white;
        }

        .modal-btn-confirm:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .modal-overlay.hiding {
            animation: fadeOut 0.2s ease-in forwards;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h2>Redactar Concepto Jurídico</h2>
            <a href="{{ route('conceptos.create') }}" class="btn-back">
                <i class="fas fa-chevron-left" style="margin-right: 15px"></i>
                Volver a la Lista
            </a>
        </div>

        <!-- Alert -->
        @if (session('success'))
            <div id="successAlert" class="alert show">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.classList.remove('show')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

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
                                <i class="fas fa-hashtag" style="color: #3b82f6;"></i>
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
                            <i class="fas fa-heading icon-modern"></i>
                            Título del Concepto <span class="required">*</span>
                        </label>

                        <div class="input-wrapper-modern">
                            <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}"
                                class="form-input-modern"
                                placeholder="Ejemplo: Análisis de la responsabilidad civil en el caso" maxlength="120"
                                required>
                        </div>

                        <p class="form-help-modern">
                            Escribe un título breve y descriptivo para el concepto (máx. 120 caracteres).
                        </p>
                    </div>

                    <!-- Concepto Jurídico Principal -->
                    <div class="form-group">
                        <label for="concepto" class="form-label">
                            <i class="fas fa-gavel" style="color: #3b82f6;"></i>
                            Concepto Jurídico
                            <span class="required">*</span>
                        </label>
                        <div class="form-help">
                            Redacta un análisis jurídico completo y fundamentado del caso (mínimo 50 caracteres).
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

    <script>
        // VARIABLES GLOBALES
        let formToSubmit = null;

        // FUNCIONES DEL MODAL
        function showModal(form) {
            formToSubmit = form;
            const modal = document.getElementById('confirmModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.add('hiding');

            setTimeout(() => {
                modal.classList.remove('show', 'hiding');
                document.body.style.overflow = '';
                formToSubmit = null;
            }, 200);
        }

        function confirmSubmit() {
            if (formToSubmit) {
                formToSubmit.removeEventListener('submit', handleFormSubmit);
                formToSubmit.submit();
            }
            closeModal();

            window.location.href = previousUrl;
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            showModal(this);
        }

        // CÓDIGO PRINCIPAL
        document.addEventListener('DOMContentLoaded', function() {
            const conceptoTextarea = document.getElementById('concepto');
            const counter = document.getElementById('conceptoCounter');
            const submitBtn = document.getElementById('submitBtn');
            const errorDiv = document.getElementById('conceptoError');
            const form = document.getElementById('conceptoForm');
            const modal = document.getElementById('confirmModal');

            // Contador de caracteres
            conceptoTextarea.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = length + ' caracteres';

                if (length < 50) {
                    counter.className = 'char-counter error';
                    submitBtn.disabled = true;
                    errorDiv.classList.add('show');
                    this.classList.add('error');
                } else {
                    counter.className = 'char-counter success';
                    submitBtn.disabled = false;
                    errorDiv.classList.remove('show');
                    this.classList.remove('error');
                }
            });

            // Trigger inicial
            conceptoTextarea.dispatchEvent(new Event('input'));

            // Evento del formulario
            if (form) {
                form.addEventListener('submit', handleFormSubmit);
            }

            // Cerrar modal al hacer clic fuera
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Cerrar con tecla ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>
</body>

</html>
