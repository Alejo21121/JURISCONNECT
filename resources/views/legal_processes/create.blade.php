<!DOCTYPE html>
<html lang="es"><!-- pagina para crear un nuevo proceso judicial -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Proceso Judicial</title>
    <!-- Enlace a CSS corregido -->
    <link rel="stylesheet" href="{{ asset('css/createPro.css') }}">
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
                        Crear Nuevo Proceso Judicial
                    </h1>
                    <p>Complete los datos del proceso judicial</p>
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
                                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <svg class="label-icon demandante-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                    Demandante
                                </span>
                            </label>
                            <input type="text" name="demandante" id="demandante" class="form-input demandante-input"
                                placeholder="Nombre completo del demandante">
                        </div>
                        <!-- Demandado -->
                        <div class="field-group">
                            <label for="demandado" class="field-label">
                                <span class="label-content">
                                    <svg class="label-icon demandado-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                    Demandado
                                </span>
                            </label>
                            <input type="text" name="demandado" id="demandado" class="form-input demandado-input"
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
                            <input type="number" name="valor_estimado" class="form-control"
                                placeholder="Ej: 1500000" min="0" step="0.01">
                        </div>

                    </div>
                </div>
                <!--  -->
                <div class="field-group">
                    <label for="descripcion" class="field-label">
                        <span class="label-content">
                            <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <label for="documento" class="field-label" style="color: #1d4ed8;">
                            Documento inicial del proceso
                        </label>
                        <div class="file-input">
                            <input type="file" name="documentos[]" id="documento" class="form-input" multiple>
                        </div>

                        <div id="filePreview" class="file-preview"></div>
                        <p class="file-help">Formatos permitidos: PDF, DOC, DOCX. Tamaño máximo: 10MB</p>
                    </div>
                </div>
                <!-- Botones -->
                <div class="button-container">
                    <a href="{{ route('dashboard.abogado') }}" class="btn btn-cancel">
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
</body>

</html>

<script>
    // Envío del formulario con AJAX después de validación
    function submitFormularioAjax() {
        const formData = new FormData(document.getElementById('formProceso'));

        archivosSeleccionados.forEach(file => {
            formData.append('documentos[]', file);
        });

        fetch('{{ route('procesos.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showAlert('success', '¡Éxito!', 'Proceso creado exitosamente. Redirigiendo...');
                    setTimeout(() => {
                        window.location.href = '{{ route('dashboard.abogado') }}';
                    }, 1500);
                } else {
                    if (result.errors) {
                        let errores = [];
                        for (let campo in result.errors) {
                            errores.push(result.errors[campo].join(', '));
                        }
                        showAlert('error', 'Errores de validación', errores.join('\n'));
                    } else {
                        showAlert('error', 'Error', result.message || 'Error desconocido');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión', 'No se pudo conectar con el servidor');
            });
    }

    // Función para mostrar alertas personalizadas
    function showAlert(type, title, message, buttons = null) {
        const overlay = document.getElementById('alertOverlay');
        const alert = document.getElementById('customAlert');
        const icon = document.getElementById('alertIcon');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const alertButtons = document.getElementById('alertButtons');

        // Remover clases anteriores
        alert.className = 'custom-alert';
        alert.classList.add(`alert-${type}`);

        // Configurar icono según el tipo
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        icon.textContent = icons[type] || '✓';

        // Configurar contenido
        alertTitle.textContent = title;
        alertMessage.textContent = message;

        // Configurar botones
        if (buttons) {
            alertButtons.innerHTML = buttons;
        } else {
            alertButtons.innerHTML = `<button class="alert-button ${type}" onclick="closeAlert()">Aceptar</button>`;
        }

        // Mostrar overlay
        overlay.classList.add('show');
    }

    // Función para cerrar alerta
    function closeAlert() {
        const overlay = document.getElementById('alertOverlay');
        overlay.classList.remove('show');
    }

    // Cerrar con ESC o click fuera
    document.getElementById('alertOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeAlert();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAlert();
    });

    // Validación del formulario
    function validarFormulario(e) {
        e.preventDefault();

        // Limpiar errores previos
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(el => {
            el.classList.remove('error', 'success');
        });

        let errores = [];
        let valido = true;

        // Validar tipo de proceso
        const tipoProceso = document.getElementById('tipo_proceso');
        if (!tipoProceso.value) {
            errores.push('Debe seleccionar un tipo de proceso');
            tipoProceso.classList.add('error');
            document.getElementById('error_tipo_proceso').textContent = 'Campo requerido';
            valido = false;
        } else {
            tipoProceso.classList.add('success');
        }

        // Validar número de radicado
        const numeroRadicado = document.getElementById('numero_radicado');
        if (!numeroRadicado.value.trim()) {
            errores.push('El número de radicado es obligatorio');
            numeroRadicado.classList.add('error');
            document.getElementById('error_numero_radicado').textContent = 'Campo requerido';
            valido = false;
        } else {
            numeroRadicado.classList.add('success');
        }

        // Validar demandante
        const demandante = document.getElementById('demandante');
        if (!demandante.value.trim()) {
            errores.push('El nombre del demandante es obligatorio');
            demandante.classList.add('error');
            document.getElementById('error_demandante').textContent = 'Campo requerido';
            valido = false;
        } else {
            demandante.classList.add('success');
        }

        // Validar demandado
        const demandado = document.getElementById('demandado');
        if (!demandado.value.trim()) {
            errores.push('El nombre del demandado es obligatorio');
            demandado.classList.add('error');
            document.getElementById('error_demandado').textContent = 'Campo requerido';
            valido = false;
        } else {
            demandado.classList.add('success');
        }

        // Validar 
        const descripcion = document.getElementById('descripcion');
        if (!descripcion.value.trim()) {
            errores.push('La  del caso es obligatoria');
            descripcion.classList.add('error');
            document.getElementById('error_descripcion').textContent = 'Campo requerido';
            valido = false;
        } else if (descripcion.value.trim().length < 20) {
            errores.push('La  debe tener al menos 20 caracteres');
            descripcion.classList.add('error');
            document.getElementById('error_descripcion').textContent = 'Mínimo 20 caracteres';
            valido = false;
        } else {
            descripcion.classList.add('success');
        }

        // Validar documento (opcional pero validar formato si existe)
        const documento = document.getElementById('documento');
        if (documento.files.length > 0) {

            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            const maxSize = 10 * 1024 * 1024; // 10MB

            for (let i = 0; i < documento.files.length; i++) {
                const file = documento.files[i];

                if (!allowedTypes.includes(file.type)) {
                    errores.push(`El archivo "${file.name}" no es PDF, DOC o DOCX`);
                    documento.classList.add('error');
                    valido = false;
                    break;
                }

                if (file.size > maxSize) {
                    errores.push(`El archivo "${file.name}" supera los 10MB`);
                    documento.classList.add('error');
                    valido = false;
                    break;
                }
            }

            if (valido) {
                documento.classList.add('success');
            }
        }

        // Mostrar resultado de la validación
        if (!valido) {
            let mensajeErrores = errores.length > 1 ?
                `Se encontraron ${errores.length} errores:\n\n${errores.map((e, i) => `${i + 1}. ${e}`).join('\n')}` :
                errores[0];

            showAlert('error', '¡Error de Validación!', mensajeErrores);

            // Scroll al primer error
            const primerError = document.querySelector('.error');
            if (primerError) {
                primerError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        } else {
            // Formulario válido - Enviar directamente
            enviarFormulario();
        }

        return false;
    }

    // Función para enviar el formulario
    function enviarFormulario() {
        submitFormularioAjax();
    }

    // Asignar evento al formulario
    document.getElementById('formProceso').addEventListener('submit', validarFormulario);

    // Limpiar errores al escribir
    document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error');
            const errorSpan = document.getElementById('error_' + this.id);
            if (errorSpan) {
                errorSpan.textContent = '';
            }
        });
    });

    // Demo: Botones para probar las alertas    
    function testAlerts() {
        console.log('Alertas disponibles:');
        console.log('1. showAlert("success", "Éxito", "Operación completada")');
        console.log('2. showAlert("error", "Error", "Algo salió mal")');
        console.log('3. showAlert("warning", "Advertencia", "Tenga cuidado")');
        console.log('4. showAlert("info", "Información", "Datos importantes")');
    }

    let archivosSeleccionados = [];

    const inputArchivo = document.getElementById('documento');
    const preview = document.getElementById('filePreview');

    inputArchivo.addEventListener('change', function() {
        for (let file of this.files) {
            archivosSeleccionados.push(file);
        }
        actualizarPreview();
        this.value = ''; // permite volver a elegir el mismo archivo
    });

    function actualizarPreview() {
        preview.innerHTML = '';

        archivosSeleccionados.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'file-item';

            div.innerHTML = `
            <div class="file-name">
                📄 ${file.name}
            </div>
            <button type="button" class="file-remove" onclick="eliminarArchivo(${index})">
                Eliminar
            </button>
        `;

            preview.appendChild(div);
        });
    }

    function eliminarArchivo(index) {
        archivosSeleccionados.splice(index, 1);
        actualizarPreview();
    }

    const requierePagoSelect = document.getElementById('requiere_pago');
    const valorEstimadoDiv = document.getElementById('valorEstimadoDiv');
    const valorEstimadoInput = valorEstimadoDiv.querySelector('input');

    function toggleValorEstimado() {
        if (requierePagoSelect.value === '1') {
            valorEstimadoDiv.classList.remove('d-none');
            valorEstimadoInput.setAttribute('required', 'required');
        } else {
            valorEstimadoDiv.classList.add('d-none');
            valorEstimadoInput.removeAttribute('required');
            valorEstimadoInput.value = '';
        }
    }

    // Al cambiar el select
    requierePagoSelect.addEventListener('change', toggleValorEstimado);

    // 🚨 CLAVE: al cargar la página
    document.addEventListener('DOMContentLoaded', toggleValorEstimado);
</script>
