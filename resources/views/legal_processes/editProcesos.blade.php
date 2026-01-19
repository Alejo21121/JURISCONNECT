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
                            <select class="form-select" name="estado" id="estadoSelect" required>
                                @foreach (['Pendiente', 'Radicado', 'Admisión', 'Traslado', 'Audiencia', 'Fallo favorable', 'Fallo desfavorable', 'Apelación', 'Ejecutoria', 'Conciliado', 'Archivado'] as $estado)
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

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">¿Requiere pago? *</label>
                            <select class="form-select" name="requiere_pago" id="requiere_pago" required>
                                <option value="0" {{ $proceso->requiere_pago == 0 ? 'selected' : '' }}>
                                    No
                                </option>
                                <option value="1" {{ $proceso->requiere_pago == 1 ? 'selected' : '' }}>
                                    Sí
                                </option>
                            </select>
                        </div>

                        <div class="form-group" id="valor_pago_box" style="display:none;">
                            <label class="form-label">Valor estimado *</label>
                            <input type="number" class="form-control" name="valor_estimado" id="valor_estimado"
                                min="0" step="0.01"
                                value="{{ old('valor_estimado', $proceso->valor_estimado) }}">
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
                            <input type="file" id="documento" multiple>
                            <label for="documento" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Seleccionar archivo</span>
                            </label>
                        </div>

                        <div id="filePreview" class="file-preview"></div>
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
    <script src="{{ asset('js/editPro.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
