<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redactar Concepto Jurídico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            color: #1a202c;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }

        .header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-back:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        /* Alert */
        .alert {
            background: linear-gradient(135deg, #f0fff4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 2rem;
            color: #166534;
            display: none;
        }

        .alert.show {
            display: flex;
            align-items: center;
        }

        .alert button {
            background: none;
            border: none;
            color: #16a34a;
            cursor: pointer;
            margin-left: auto;
            padding: 0.25rem;
        }

        /* Card Base */
        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* Card Headers */
        .card-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header.primary {
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
        }

        .card-header h5 {
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge {
            background: #fbbf24;
            color: #92400e;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        /* Card Body */
        .card-body {
            padding: 2rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .info-item {
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            gap: 0.5rem;
        }

        .info-value {
            margin-left: 1.5rem;
            color: #6b7280;
            font-size: 1rem;
        }

        .info-value.primary {
            color: #3b82f6;
            font-weight: 700;
            font-size: 1.125rem;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: flex;
            align-items: center;
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
            gap: 0.5rem;
        }

        .required {
            color: #ef4444;
        }

        .optional {
            color: #6b7280;
            font-weight: 400;
            font-size: 0.875rem;
            margin-left: 0.5rem;
        }

        .form-help {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #3b82f6;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #1e40af;
        }

        .form-help.warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
            border-left-color: #f59e0b;
            color: #92400e;
        }

        /* Textarea */
        .form-textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #d1d5db;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-family: inherit;
            resize: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea.error {
            border-color: #ef4444;
        }

        .form-textarea.error:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Textarea Container */
        .textarea-container {
            position: relative;
        }

        .char-counter {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #6b7280;
        }

        .char-counter.error {
            background: #fee2e2;
            color: #dc2626;
        }

        .char-counter.success {
            background: #dcfce7;
            color: #16a34a;
        }

        .form-error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
        }

        .form-error.show {
            display: block;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-cancel {
            background: white;
            color: #374151;
            border: 2px solid #d1d5db;
        }

        .btn-cancel:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-draft {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1d4ed8;
            border: 2px solid #93c5fd;
        }

        .btn-draft:hover {
            background: linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%);
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: 2px solid transparent;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .btn-submit:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-submit:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .btn-actions {
            display: flex;
            gap: 0.75rem;
        }

        /* Help Panel */
        .help-panel {
            background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            overflow: hidden;
        }

        .help-header {
            background: rgba(255, 255, 255, 0.8);
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .help-header h6 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .help-body {
            padding: 2rem;
        }

        .help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .help-section h6 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid;
        }

        .help-section:first-child h6 {
            border-bottom-color: #bbf7d0;
        }

        .help-section:last-child h6 {
            border-bottom-color: #fed7aa;
        }

        .help-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .help-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
        }

        .help-item.success {
            border-left-color: #10b981;
        }

        .help-item.warning {
            border-left-color: #f59e0b;
        }

        .help-item i {
            margin-right: 0.75rem;
            margin-top: 0.125rem;
        }

        .help-item span {
            color: #374151;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .header h2 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-actions {
                flex-direction: column;
                width: 100%;
            }

            .help-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Alert -->
        <div id="successAlert" class="alert">
            <i class="fas fa-check-circle"></i>
            <span>Concepto guardado exitosamente</span>
            <button onclick="this.parentElement.classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>

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
                <form id="conceptoForm">
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
                            <textarea id="concepto" name="concepto" rows="12" class="form-textarea"
                                placeholder="Ingresa aquí el análisis jurídico detallado del proceso...
                                Estructura sugerida:
                                1. Análisis de hechos
                                2. Marco jurídico aplicable
                                3. Análisis legal
                                4. Conclusiones"
                                required></textarea>
                            <div id="conceptoCounter" class="char-counter">0 caracteres</div>
                        </div>
                        <div id="conceptoError" class="form-error">
                            El concepto debe tener al menos 50 caracteres.
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="btn-group">
                        <a href="{{ route('conceptos.create') }}" class="btn btn-cancel">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                        </a>

                        <div class="btn-actions">
                            <button type="button" onclick="guardarBorrador()" class="btn btn-draft">
                                <i class="fas fa-save"></i>
                                Guardar Borrador
                            </button>
                            <button type="submit" id="submitBtn" disabled class="btn btn-submit">
                                <i class="fas fa-check"></i>
                                Finalizar Concepto
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const conceptoTextarea = document.getElementById('concepto');
                const counter = document.getElementById('conceptoCounter');
                const submitBtn = document.getElementById('submitBtn');
                const errorDiv = document.getElementById('conceptoError');

                // Contador de caracteres
                conceptoTextarea.addEventListener('input', function() {
                    const length = this.value.length;
                    counter.textContent = length + ' caracteres';

                    // Cambiar color según la longitud
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

                // Confirmación antes de enviar
                document.getElementById('conceptoForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm(
                            '¿Estás seguro de que deseas finalizar este concepto? Una vez enviado, no podrás modificarlo.'
                            )) {
                        // Simular envío exitoso
                        document.getElementById('successAlert').classList.add('show');
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            function guardarBorrador() {
                // Mostrar mensaje temporal
                const originalText = event.target.innerHTML;
                event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                event.target.disabled = true;

                setTimeout(() => {
                    event.target.innerHTML = '<i class="fas fa-check"></i> Guardado';
                    setTimeout(() => {
                        event.target.innerHTML = originalText;
                        event.target.disabled = false;
                    }, 1000);
                }, 1500);
            }
        </script>
</body>

</html>
