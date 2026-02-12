<!-- Tabla -->
<link rel="stylesheet" href="{{ asset('css/pro-table.css') }}">
<div class="table-container">

    @if (session('success'))
        <div id="successOverlay" class="alert-overlay"></div>

        <div id="successModal" class="alert-modal">
            <div class="alert-icon" style="color: #28a745;">
                ✅
            </div>

            <h2>Operación exitosa</h2>

            <p>
                {{ session('success') }}
            </p>

            <div class="alert-buttons">
                <button class="btn-confirm" onclick="cerrarSuccessModal()">Aceptar</button>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('successOverlay').classList.remove('hidden');
                document.getElementById('successModal').classList.remove('hidden');
            });

            function cerrarSuccessModal() {
                document.getElementById('successOverlay').classList.add('hidden');
                document.getElementById('successModal').classList.add('hidden');
            }
        </script>
    @endif

    <div class="table-wrapper">
        <table class="process-table">
            <!-- titulos de la tabla -->
            <thead class="table-header">
                <tr>
                    <th>
                        <div class="header-content-cell">
                            <span>Radicado</span>
                            <svg class="header-icon-small" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </th>
                    @auth
                        @if (Auth::user()->role_id == 3)
                            <th>
                                <div class="header-content-cell">
                                    <span>Abogado</span>
                                    <svg class="header-icon-small" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </th>
                        @endif
                    @endauth
                    <th>
                        <div class="header-content-cell">
                            <span>Tipo</span>
                            <svg class="header-icon-small" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                    </th>
                    <th>
                        <div class="header-content-cell">
                            <span>Demandante</span>
                            <svg class="header-icon-small header-icon-green" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </th>
                    <th>
                        <div class="header-content-cell">
                            <span>Demandado</span>
                            <svg class="header-icon-small header-icon-red" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </th>

                    <th>
                        <div class="header-content-cell">
                            <span>Estado </span>
                            <span>📊</span>
                        </div>
                    </th>

                    <th class="actions-cell">
                        <div class="header-content-cell" style="justify-content: center;">
                            <span>Acciones</span>
                            <svg class="header-icon-small" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                </path>
                            </svg>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody class="table-body" id=tableBody>
                @forelse($procesos as $proceso)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $proceso->numero_radicado }}</td>
                        @auth
                            @if (Auth::user()->role_id == 3)
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $proceso->lawyer->user->name ?? 'Sin abogado' }}
                                </td>
                            @endif
                        @endauth
                        <td class="border border-gray-300 px-4 py-2">{{ $proceso->tipo_proceso }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $proceso->demandante }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $proceso->demandado }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <span class="estado-badge estado-{{ Str::slug($proceso->estado) }}">
                                @switch($proceso->estado)
                                    @case('Conciliado')
                                        <i class="fas fa-handshake"></i>
                                    @break

                                    @case('Pendiente')
                                        <i class="fas fa-handshake"></i>
                                    @break

                                    @case('Archivado')
                                        <i class="fas fa-archive"></i>
                                    @break

                                    @case('Suspendido')
                                        <i class="fas fa-pause-circle"></i>
                                    @break

                                    @case('Finalizado')
                                        <i class="fas fa-check-circle"></i>
                                    @break

                                    @default
                                        <i class="fas fa-info-circle"></i>
                                @endswitch
                                {{ $proceso->estado }}
                            </span>
                        </td>


                        <td class="actions-cell">
                            <div class="actions-group">
                                <!-- Modifica el botón "Ver detalles" para llamar a la función -->
                                <a href="javascript:void(0);"
                                    onclick="openProcessModal({{ json_encode($proceso->id) }})"
                                    class="action-btn action-view" title="Ver detalles">
                                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </a>

                                @if ($proceso->estado === 'Archivado')
                                    <button class="action-btn action-edit opacity-50 cursor-not-allowed"
                                        onclick="mostrarProcesoArchivado()" title="No editable">
                                        <svg class="action-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m0-8v2m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z" />
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ route('procesos.edit', $proceso->id) }}"
                                        class="action-btn action-edit" title="Editar">
                                        <svg class="action-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                @endif


                                <form action="{{ route('procesos.destroy', $proceso->id) }}" method="POST"
                                    class="form-delete-proceso" data-nombre="{{ $proceso->demandante }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="action-btn action-delete" title="Eliminar">
                                        <svg class="action-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-500">No hay procesos registrados</td>
                        </tr>
                    @endforelse
                </tbody>

                <div id="alertOverlay" class="alert-overlay hidden"></div>

                <div id="customAlert" class="alert-modal hidden">
                    <div class="alert-icon">
                        ⚠️
                    </div>
                    <h2 id="alertTitle">Confirmar Eliminación</h2>
                    <p id="alertMessage">
                        ¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.
                    </p>

                    <div class="alert-buttons">
                        <button id="alertCancel" class="btn-cancel">Cancelar</button>
                        <button id="alertConfirm" class="btn-confirm">Eliminar</button>
                    </div>
                </div>

                <!-- Modal Proceso Archivado -->
                <div id="archivadoOverlay" class="alert-overlay hidden"></div>

                <div id="archivadoModal" class="alert-modal hidden">
                    <div class="alert-icon">📁</div>

                    <h2>Proceso archivado</h2>

                    <p>
                        Este proceso está <strong>archivado</strong> y no se puede editar.<br>
                        Si necesita realizar algún cambio, por favor contacte al administrador.
                    </p>

                    <div class="alert-buttons">
                        <button class="btn-confirm" onclick="cerrarProcesoArchivado()">Entendido</button>
                    </div>
                </div>


                <script>
                    // Función para abrir el modal de detalles del proceso
                    function openProcessModal(procesId) {
                        // Implementar lógica para abrir modal con detalles del proceso
                        console.log('Abrir detalles del proceso:', procesId);
                    }

                    // Función para mostrar el modal
                    function showDeleteConfirm(name, onConfirm) {
                        const overlay = document.getElementById('alertOverlay');
                        const modal = document.getElementById('customAlert');
                        const title = document.getElementById('alertTitle');
                        const message = document.getElementById('alertMessage');
                        const cancelBtn = document.getElementById('alertCancel');
                        const confirmBtn = document.getElementById('alertConfirm');

                        title.textContent = "Confirmar Eliminación";
                        message.textContent = `¿Estás seguro de eliminar el proceso de ${name}? Esta acción no se puede deshacer.`;

                        overlay.classList.remove('hidden');
                        modal.classList.remove('hidden');

                        cancelBtn.onclick = () => {
                            overlay.classList.add('hidden');
                            modal.classList.add('hidden');
                        };

                        confirmBtn.onclick = () => {
                            overlay.classList.add('hidden');
                            modal.classList.add('hidden');
                            onConfirm();
                        };
                    }

                    // Interceptar formularios de eliminar
                    document.querySelectorAll('.form-delete-proceso').forEach(form => {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault(); // Detiene el envío inmediato

                            let nombre = form.dataset.nombre || "este proceso";

                            showDeleteConfirm(nombre, () => {
                                form.submit(); // Enviar después de confirmar
                            });
                        });
                    });

                    function mostrarProcesoArchivado() {
                        document.getElementById('archivadoOverlay').classList.remove('hidden');
                        document.getElementById('archivadoModal').classList.remove('hidden');
                    }

                    function cerrarProcesoArchivado() {
                        document.getElementById('archivadoOverlay').classList.add('hidden');
                        document.getElementById('archivadoModal').classList.add('hidden');
                    }
                </script>


            </table>
        </div>

        <!-- Paginación -->
        <div class="pagination-container">
            <div class="pagination-content">
                <!-- Paginación móvil -->
                <div class="pagination-mobile">
                    @if ($procesos->onFirstPage())
                        <span class="pagination-btn disabled">Anterior</span>
                    @else
                        <a href="{{ $procesos->previousPageUrl() }}" class="pagination-btn">Anterior</a>
                    @endif

                    <!-- Números de página -->
                    @php
                        $currentPage = $procesos->currentPage();
                        $lastPage = $procesos->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $procesos->url(1) }}" class="pagination-btn">1</a>
                        @if ($start > 2)
                            <span class="pagination-btn disabled">...</span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $currentPage)
                            <span class="pagination-btn active">{{ $i }}</span>
                        @else
                            <a href="{{ $procesos->url($i) }}" class="pagination-btn">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="pagination-btn disabled">...</span>
                        @endif
                        <a href="{{ $procesos->url($lastPage) }}" class="pagination-btn">{{ $lastPage }}</a>
                    @endif

                    @if ($procesos->hasMorePages())
                        <a href="{{ $procesos->nextPageUrl() }}" class="pagination-btn">Siguiente</a>
                    @else
                        <span class="pagination-btn disabled">Siguiente</span>
                    @endif
                </div>

                <!-- Paginación desktop -->
                <div class="pagination-desktop">

                    <div style="display: flex; gap: 0.5rem;">
                        <!-- Botón Anterior -->
                        @if ($procesos->onFirstPage())
                            <span class="pagination-btn disabled">Anterior</span>
                        @else
                            <a href="{{ $procesos->previousPageUrl() }}" class="pagination-btn">Anterior</a>
                        @endif

                        <!-- Números de página -->
                        @php
                            $currentPage = $procesos->currentPage();
                            $lastPage = $procesos->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp

                        @if ($start > 1)
                            <a href="{{ $procesos->url(1) }}" class="pagination-btn">1</a>
                            @if ($start > 2)
                                <span class="pagination-btn disabled">...</span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $currentPage)
                                <span class="pagination-btn active">{{ $i }}</span>
                            @else
                                <a href="{{ $procesos->url($i) }}" class="pagination-btn">{{ $i }}</a>
                            @endif
                        @endfor

                        @if ($end < $lastPage)
                            @if ($end < $lastPage - 1)
                                <span class="pagination-btn disabled">...</span>
                            @endif
                            <a href="{{ $procesos->url($lastPage) }}" class="pagination-btn">{{ $lastPage }}</a>
                        @endif

                        <!-- Botón Siguiente -->
                        @if ($procesos->hasMorePages())
                            <a href="{{ $procesos->nextPageUrl() }}" class="pagination-btn">Siguiente</a>
                        @else
                            <span class="pagination-btn disabled">Siguiente</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
