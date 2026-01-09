<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proceso Judicial - CSS Puro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/editPro.css') }}">
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fas fa-balance-scale"></i>
                <span>Sistema Jurídico</span>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="main-card fade-in-up">

            <div class="card-header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Editar Proceso Judicial
                </h1>
                <a href="#" class="back-btn" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <div class="card-body">

                <form class="form" method="POST" enctype="multipart/form-data"
                    action="{{ route('procesos.update', $proceso->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- ESTADO -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Estado de proceso *</label>
                            <select class="form-select" name="estado" required>
                                @foreach (['Pendiente', 'Primera instancia', 'En curso', 'Finalizado', 'En audiencia', 'Pendiente fallo', 'Favorable primera', 'Desfavorable primera', 'En apelacion', 'Conciliacion pendiente', 'Conciliado', 'Sentencia ejecutoriada', 'En proceso pago', 'Terminado'] as $estado)
                                    <option value="{{ $estado }}"
                                        {{ old('estado', $proceso->estado) == $estado ? 'selected' : '' }}>
                                        {{ $estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- TIPO / RADICADO -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tipo de Proceso *</label>
                            <select class="form-select" name="tipo_proceso" required>
                                @foreach (['Civil', 'Penal', 'Laboral', 'Administrativo', 'Familia', 'Comercial'] as $tipo)
                                    <option value="{{ $tipo }}"
                                        {{ old('tipo_proceso', $proceso->tipo_proceso) == $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Número de Radicado *</label>
                            <input type="text" class="form-control bg-light" value="{{ $proceso->numero_radicado }}"
                                readonly>
                            <input type="hidden" name="numero_radicado" value="{{ $proceso->numero_radicado }}">
                        </div>
                    </div>

                    <!-- PARTES -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Demandante *</label>
                            <input type="text" class="form-control" name="demandante"
                                value="{{ old('demandante', $proceso->demandante) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Demandado *</label>
                            <input type="text" class="form-control" name="demandado"
                                value="{{ old('demandado', $proceso->demandado) }}" required>
                        </div>
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="form-group full-width">
                        <label class="form-label">Detalle del caso *</label>
                        <textarea class="form-textarea" name="descripcion" rows="4" required>{{ old('descripcion', $proceso->descripcion) }}</textarea>
                    </div>

                    <!-- DOCUMENTOS EXISTENTES -->
                    @if ($proceso->documentos->count())
                        <hr>
                        <h3 style="margin-bottom:15px;">📎 Documentos del proceso</h3>

                        <div class="documents-list">
                            @foreach ($proceso->documentos as $doc)
                                <div class="doc-item">
                                    <div class="doc-info">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>{{ $doc->nombre }}</span>
                                    </div>

                                    <div class="doc-actions">
                                        <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn-view">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <button type="button" class="btn-delete"
                                            onclick="eliminarDocumento({{ $doc->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- SUBIR NUEVOS DOCUMENTOS -->
                    <div class="form-group full-width">
                        <label class="form-label">
                            <i class="fas fa-file-upload"></i> Subir nuevos documentos
                        </label>

                        <div class="file-input">
                            <input type="file" id="documento" name="documentos[]" multiple>
                            <label for="documento" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Seleccionar archivo</span>
                            </label>
                        </div>
                    </div>

                    <!-- BOTÓN FINAL -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Proceso
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Modal eliminar documento -->
    <div id="modalEliminar" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon">
                <i class="fas fa-triangle-exclamation"></i>
            </div>

            <h3>¿Eliminar este documento?</h3>
            <p>Este documento se eliminara de forma inmediata,<br>
                 incluso si no guarda el proceso.</p>

            <div class="modal-actions">
                <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button class="btn-confirm" onclick="confirmarEliminar()">Eliminar</button>
            </div>
        </div>
    </div>
    <!-- Scripts -->

    <script>
        let documentoAEliminar = null;

        function eliminarDocumento(id) {
            documentoAEliminar = id;
            document.getElementById('modalEliminar').style.display = 'flex';
        }

        function cerrarModal() {
            documentoAEliminar = null;
            document.getElementById('modalEliminar').style.display = 'none';
        }

        function confirmarEliminar() {
            fetch(`/documentos/${documentoAEliminar}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (res.ok) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el documento');
                    }
                });
        }
        // Función para mostrar/ocultar alertas
        function showErrorAlert() {
            document.getElementById('error-alert').style.display = 'block';
            document.getElementById('error-alert').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Función para mostrar estado de carga
        function showLoading(button) {
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
            button.disabled = true;
            // Simular carga (en aplicación real esto se manejaría con el envío del formulario)
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> ¡Actualizado!';
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-save"></i> Actualizar Proceso';
                    button.disabled = false;
                }, 1000);
            }, 2000);
        }

        // Mejorar la experiencia del file input
        document.getElementById('documento').addEventListener('change', function(e) {
            const label = document.querySelector('.file-input-label span');
            if (e.target.files.length > 0) {
                label.textContent = `Archivo seleccionado: ${e.target.files[0].name}`;
                label.parentElement.style.borderColor = '#3b82f6';
                label.parentElement.style.background = '#eff6ff';
                label.parentElement.style.color = '#3b82f6';
            } else {
                label.textContent = 'Seleccionar nuevo archivo o arrastra aquí';
            }
        });

        // Drag and drop para el file input
        const fileInput = document.querySelector('.file-input');
        const fileInputLabel = document.querySelector('.file-input-label');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileInput.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            fileInput.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileInput.addEventListener(eventName, unhighlight, false);
        });

        fileInput.addEventListener('drop', handleDrop, false);

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            fileInputLabel.style.borderColor = '#3b82f6';
            fileInputLabel.style.background = '#eff6ff';
            fileInputLabel.style.color = '#3b82f6';
        }

        function unhighlight(e) {
            fileInputLabel.style.borderColor = '#d1d5db';
            fileInputLabel.style.background = '#f9fafb';
            fileInputLabel.style.color = '#6b7280';
        }

        function handleDrop(e) {
            const files = e.dataTransfer.files;
            document.getElementById('documento').files = files;

            if (files.length > 0) {
                const label = document.querySelector('.file-input-label span');
                label.textContent = `Archivo seleccionado: ${files[0].name}`;
            }
        }

        // Validación en tiempo real
        document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
            field.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });

        // Ejemplo de mostrar alerta de error (descomenta para probar)
        // setTimeout(() => showErrorAlert(), 1000);
    </script>
</body>

</html>
