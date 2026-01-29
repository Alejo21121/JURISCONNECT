<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conceptos Jurídicos - Sistema Jurídico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/editCon.css') }}">
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
        <div class="header">
            <div class="header-content">
                <h1>Conceptos Jurídicos</h1>
                <p>Listado de los conceptos asociados al proceso seleccionado</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('conceptos.create') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i>
                    Volver al listado
                </a>
            </div>
        </div>

        @if (isset($conceptos) && $conceptos->isNotEmpty())
            <div class="concepts-container">
                <div class="concepts-list">
                    <div class="list-header fade-in-up">
                        <h4>Conceptos del proceso:</h4>
                        <p class="process-number">{{ $proceso->numero_radicado ?? 'ID ' . $proceso->id }}</p>
                    </div>

                    @foreach ($conceptos as $c)
                        <div class="concept-card fade-in-up">
                            <h5>{{ $c->titulo }}</h5>
                            <div class="concept-meta">
                                Redactado por: {{ $c->abogado->name ?? ($c->abogado->user->name ?? '—') }} ·
                                {{ $c->created_at->format('d M Y') ?? '' }}
                            </div>
                            <p>{{ \Illuminate\Support\Str::limit($c->descripcion ?? ($c->concepto ?? ''), 300) }}</p>

                            <div class="concept-actions">
                                <a href="{{ route('concepto.show', $c->id) }}" class="btn btn-view btn-sm">
                                    <i class="fas fa-eye"></i>
                                    Ver detalle
                                </a>

                                {{-- 🔥 SOLO MOSTRAR BOTÓN ELIMINAR SI NO ESTÁ ARCHIVADO --}}
                                @if ($proceso->estado !== 'Archivado')
                                    <button type="button" class="btn btn-delete btn-sm"
                                        onclick="showDeleteModal({{ $c->id }}, '{{ addslashes($c->titulo) }}')">
                                        <i class="fas fa-trash"></i>
                                        Eliminar
                                    </button>

                                    <form id="delete-form-{{ $c->id }}"
                                        action="{{ route('conceptos.destroy', $c->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @else
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="concept-sidebar fade-in-up">
                    <h5>Resumen</h5>
                    <div class="sidebar-item">
                        <strong>Total de conceptos</strong>
                        <p>{{ $conceptos->count() }} {{ $conceptos->count() == 1 ? 'concepto' : 'conceptos' }}</p>
                    </div>
                    <div class="sidebar-item">
                        <strong>Tipo de proceso</strong>
                        <p>{{ $proceso->tipo_proceso ?? '—' }}</p>
                    </div>
                    <div class="sidebar-item">
                        <strong>Demandante</strong>
                        <p>{{ $proceso->demandante ?? '—' }}</p>
                    </div>
                    <div class="sidebar-item">
                        <strong>Demandado</strong>
                        <p>{{ $proceso->demandado ?? '—' }}</p>
                    </div>
                </aside>
            </div>
        @elseif(isset($concepto))
            <!-- Detalle de un concepto específico -->
            <div class="detail-card fade-in-up">
                <div class="detail-header">
                    <div>
                        <div class="detail-title">{{ $concepto->titulo }}</div>
                        <div class="detail-meta">
                            <span>
                                <i class="fas fa-folder"></i>
                                Proceso: {{ $concepto->proceso->numero_radicado ?? 'ID ' . $concepto->proceso_id }}
                            </span>
                            <span>
                                <i class="fas fa-calendar"></i>
                                Redactado: {{ $concepto->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-actions">
                        <a href="javascript:history.back()" class="btn-back-modern">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver</span>
                        </a>
                    </div>
                </div>

                <div class="detail-body">
                    <div>
                        <div class="detail-section">
                            <h4>
                                <i class="fas fa-file-alt"></i>
                                Descripción
                            </h4>
                            <p>{!! nl2br(e($concepto->descripcion ?? $concepto->concepto)) !!}</p>
                        </div>

                        @if (isset($proceso))
                            <div class="detail-section">
                                <h4>
                                    <i class="fas fa-info-circle"></i>
                                    Información del Proceso
                                </h4>
                                <p><strong>Estado:</strong> {{ $proceso->estado ?? 'No registrado' }}</p>
                                <p><strong>Tipo:</strong> {{ $proceso->tipo_proceso ?? 'No especificado' }}</p>
                            </div>
                        @endif
                    </div>

                    <aside class="concept-sidebar">
                        <h5>Detalles</h5>
                        <div class="sidebar-item">
                            <strong>Abogado</strong>
                            <p>{{ auth()->user()->name ?? ($concepto->abogado->user->name ?? '—') }}</p>
                        </div>
                        <div class="sidebar-item">
                            <strong>Fecha de creación</strong>
                            <p>{{ $concepto->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @if (isset($concepto->updated_at) && $concepto->updated_at != $concepto->created_at)
                            <div class="sidebar-item">
                                <strong>Última actualización</strong>
                                <p>{{ $concepto->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        @endif
                        @if ($concepto->documento)
                            <div class="sidebar-item">
                                <strong>Documento adjunto</strong>
                                <p>
                                    <a href="{{ asset('storage/' . $concepto->documento) }}" target="_blank"
                                        class="btn btn-view btn-sm" style="display: inline-flex; margin-top: 0.5rem;">
                                        <i class="fas fa-download"></i>
                                        Ver documento
                                    </a>
                                </p>
                            </div>
                        @endif
                    </aside>
                </div>
            </div>
        @else
            <!-- Estado vacío -->
            <div class="empty-state fade-in-up">
                <div class="empty-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3>No hay conceptos disponibles</h3>
                <p>No se encontraron conceptos para mostrar en este proceso.</p>
                <a href="{{ route('procesos.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Volver al listado de procesos
                </a>
            </div>
        @endif
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="modal-title">¿Eliminar concepto jurídico?</h3>
                <p class="modal-description">
                    Estás a punto de eliminar el concepto: <br>
                    <strong id="conceptTitle"></strong>
                </p>
                <p class="modal-warning">
                    <i class="fas fa-info-circle"></i>
                    Esta acción no se puede deshacer
                </p>
                <div class="modal-actions">
                    <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn-modal-confirm" onclick="confirmDelete()">
                        <i class="fas fa-trash"></i>
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let deleteConceptId = null;

        function showDeleteModal(id, title) {
            deleteConceptId = id;
            document.getElementById('conceptTitle').textContent = title;
            document.getElementById('deleteModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            deleteConceptId = null;
        }

        function confirmDelete() {
            if (deleteConceptId) {
                document.getElementById('delete-form-' + deleteConceptId).submit();
            }
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>

</body>

</html>
