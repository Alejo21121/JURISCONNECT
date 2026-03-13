@forelse($procesos as $proceso)
    <div class="process-card fade-in-up">
        <div class="card-header">
            <div class="card-title">
                <div class="card-title-icon">
                    <i class="fas fa-gavel"></i>
                </div>

                <h2 class="titulo-proceso" data-numero="{{ $loop->iteration }}">
                    Proceso Legal {{ $procesos->firstItem() + $loop->index }}
                    @auth
                        @if (in_array(auth()->user()->role_id, [3, 4]))
                            <span class="abogado-nombre">
                                – Abogado: {{ $proceso->lawyer->user->name ?? 'Sin asignar' }}
                            </span>
                        @endif
                    @endauth
                </h2>

            </div>
            <span class="status-badge {{ $proceso->estado === 'Archivado' ? 'status-archivado' : '' }}">
                {{ $proceso->estado }}
            </span>
        </div>

        <div class="card-body">
            <div class="card-grid">
                <div class="info-section">
                    <div class="info-item info-item-blue">
                        <div class="info-icon info-icon-blue">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="info-content">
                            <p>Radicado</p>
                            <p>{{ $proceso->numero_radicado }}</p>
                        </div>
                    </div>

                    <div class="info-item info-item-green">
                        <div class="info-icon info-icon-green">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="info-content">
                            <p>Tipo de Proceso</p>
                            <p>{{ $proceso->tipo_proceso }}</p>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-item info-item-orange">
                        <div class="info-icon info-icon-orange">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="info-content">
                            <p>Demandante</p>
                            <p>{{ $proceso->demandante }}</p>
                        </div>
                    </div>

                    <div class="info-item info-item-red">
                        <div class="info-icon info-icon-red">
                            <i class="fas fa-user-minus"></i>
                        </div>
                        <div class="info-content">
                            <p>Demandado</p>
                            <p>{{ $proceso->demandado }}</p>
                        </div>
                    </div>
                </div>

                <div class="info-item info-item-purple">
                    <div class="info-icon info-icon-purple">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <p>Fecha Radicación</p>
                        <p>{{ $proceso->created_at }}</p>
                    </div>
                </div>
            </div>

            {{-- 🚫 ALERTA SI ESTÁ ARCHIVADO --}}
            @if ($proceso->estado === 'Archivado')
                <div class="alert-archivado">
                    <i class="fas fa-lock"></i>
                    <div>
                        <strong>Proceso Archivado</strong>
                        <p>No se pueden crear nuevos conceptos jurídicos ni eliminar para este proceso.</p>
                    </div>
                </div>
            @endif

            <div class="card-footer">
                <a class="action-btn action-view" onclick="toggleHistorial({{ $proceso->id }})"
                    title="Historial del proceso">
                    Historial del proceso
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </a>

                {{-- 🔥 BOTÓN CONDICIONADO --}}
                @if ($proceso->estado === 'Archivado')
                    <button class="action-btn btn-disabled" disabled
                        title="No se pueden crear conceptos en procesos archivados">
                        <i class="fas fa-lock"></i>
                        Proceso Archivado
                    </button>
                @elseif(
                    (auth()->user()->role_id == 2 && auth()->user()->lawyer->id == $proceso->lawyer_id) ||
                        auth()->user()->role_id == 3 ||
                        (auth()->user()->role_id == 4 && auth()->user()->lawyer->id == $proceso->lawyer_id))
                    <a href="{{ route('abogado.crear-concepto', $proceso->id) }}" class="action-btn">
                        <i class="fas fa-edit"></i>
                        Redactar Concepto Jurídico
                    </a>
                @endif  

                @php
                    $conceptosColeccion = collect($proceso->conceptos ?? []);
                @endphp

                @if ($conceptosColeccion->isNotEmpty())
                    <a href="{{ route('procesos.conceptos', $proceso->id) }}" class="action-btn action-view"
                        title="Ver detalles de conceptos">
                        <i class="fa-regular fa-eye"></i>
                        Ver Detalles @if ($conceptosColeccion->count() > 1)
                            ({{ $conceptosColeccion->count() }})
                        @endif
                    </a>
                @else
                    <p class="text-muted small mb-0">No hay conceptos jurídicos aún</p>
                @endif
            </div>

            <div id="historial-{{ $proceso->id }}" class="historial-box" style="display:none;">
                <h4 class="historial-title">
                    <i class="fa-solid fa-timeline"></i>
                    Historial del proceso
                </h4>

                <table class="historial-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Observación</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proceso->historial as $item)
                            <tr>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span
                                        class="status-badge {{ $item->estado === 'Archivado' ? 'status-archivado' : '' }}">
                                        {{ $item->estado }}
                                    </span>
                                </td>
                                <td>{{ $item->observacion ?? '—' }}</td>
                                <td>{{ $item->usuario->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    No hay historial registrado
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@empty
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <h3>No se encontraron procesos pendientes.</h3>
    </div>
@endforelse


<!-- Paginación -->
<div class="pagination-desktop">

    @php
        $currentPage = $procesos->currentPage();
        $lastPage = $procesos->lastPage();
        $start = max(1, $currentPage - 2);
        $end = min($lastPage, $currentPage + 2);
    @endphp

    {{-- Botón anterior --}}
    @if ($procesos->onFirstPage())
        <span class="pagination-btn disabled">Anterior</span>
    @else
        <a href="{{ $procesos->previousPageUrl() }}" class="pagination-btn">
            Anterior
        </a>
    @endif

    {{-- Primera página si no está visible --}}
    @if ($start > 1)
        <a href="{{ $procesos->url(1) }}" class="pagination-btn">1</a>

        @if ($start > 2)
            <span class="pagination-btn disabled">...</span>
        @endif
    @endif

    {{-- Rango dinámico --}}
    @for ($i = $start; $i <= $end; $i++)
        @if ($i == $currentPage)
            <span class="pagination-btn active">{{ $i }}</span>
        @else
            <a href="{{ $procesos->url($i) }}" class="pagination-btn">
                {{ $i }}
            </a>
        @endif
    @endfor

    {{-- Última página si no está visible --}}
    @if ($end < $lastPage)
        @if ($end < $lastPage - 1)
            <span class="pagination-btn disabled">...</span>
        @endif

        <a href="{{ $procesos->url($lastPage) }}" class="pagination-btn">
            {{ $lastPage }}
        </a>
    @endif

    {{-- Botón siguiente --}}
    @if ($procesos->hasMorePages())
        <a href="{{ $procesos->nextPageUrl() }}" class="pagination-btn">
            Siguiente
        </a>
    @else
        <span class="pagination-btn disabled">Siguiente</span>
    @endif

</div>

<script>
    function toggleHistorial(id) {
        const box = document.getElementById('historial-' + id);
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }
</script>
