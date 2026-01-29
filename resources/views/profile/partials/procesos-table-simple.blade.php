<div class="table-wrapper">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <table class="lawyers-table" id="tablaProcesos">
        <thead>
            <tr>
                <th colspan="6">
                    <div style="position: relative; margin: 0 auto;">
                        <i class="fas fa-search"
                            style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none; z-index: 1;"></i>

                        <input type="text" id="buscadorProcesos" class="input-buscador"
                            placeholder="Buscar por radicado, tipo, demandante, demandado..."
                            value="{{ request('radicado') }}" style="padding-left: 40px; width: 100%;">
                    </div>
                </th>
            </tr>
            <tr>
                <th>Radicado</th>
                <th>Tipo de Proceso</th>
                <th>Abogado</th>
                <th>Demandante</th>
                <th>Demandado</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody id="procesosTableBody">
            @forelse($procesosSimple as $proceso)
                <tr>
                    <td>{{ $proceso->numero_radicado }}</td>
                    <td>{{ $proceso->tipo_proceso }}</td>
                    <td>
                        {{ optional($proceso->lawyer)->nombre }}
                        {{ optional($proceso->lawyer)->apellido }}
                    </td>
                    <td>{{ $proceso->demandante }}</td>
                    <td>{{ $proceso->demandado }}</td>
                    <td>
                        @php
                            $estadoClass = 'estado-' . \Illuminate\Support\Str::slug($proceso->estado);
                        @endphp

                        @if ($proceso->estado === 'Archivado')
                            <button class="estado-badge {{ $estadoClass }} reopen-process-btn"
                                data-id="{{ $proceso->id }}" title="Reabrir proceso">
                                {{ $proceso->estado }}
                            </button>
                        @else
                            <span class="estado-badge {{ $estadoClass }}">
                                {{ $proceso->estado }}
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:20px;">
                        No hay procesos judiciales registrados
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div id="paginationContainer">
        @include('profile.partials.pagination', [
            'items' => $procesosSimple,
            'pageKey' => 'procesosSimplePage',
        ])
    </div>

    <script>
        (function() {
            let searchTimeout;

            function buscarProcesos(page = 1) {
                const buscador = document.getElementById('buscadorProcesos');
                if (!buscador) return;

                const termino = buscador.value.trim();

                const params = new URLSearchParams();
                params.append('radicado', termino);
                params.append('procesosSimplePage', page);

                fetch(`{{ route('dashboard') }}?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.html) {
                            const temp = document.createElement('div');
                            temp.innerHTML = data.html;

                            const nuevoTbody = temp.querySelector('#procesosTableBody');
                            if (nuevoTbody) {
                                document.getElementById('procesosTableBody').innerHTML = nuevoTbody.innerHTML;
                            }

                            const nuevaPaginacion = temp.querySelector('.pagination');
                            const contenedorPaginacion = document.getElementById('paginationContainer');
                            if (nuevaPaginacion && contenedorPaginacion) {
                                contenedorPaginacion.innerHTML = nuevaPaginacion.outerHTML;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error en búsqueda:', error);
                    });
            }

            // 🔥 DELEGACIÓN DE EVENTOS - Se mantiene siempre activo
            document.body.addEventListener('click', function(e) {
                // Detectar clic en enlaces de paginación
                if (e.target.closest('#paginationContainer .pagination a')) {
                    e.preventDefault();
                    const link = e.target.closest('a');
                    const url = new URL(link.href);
                    const page = url.searchParams.get('procesosSimplePage') || 1;
                    buscarProcesos(page);
                }
            });

            // 🔥 DELEGACIÓN DE EVENTOS - Input del buscador
            document.body.addEventListener('input', function(e) {
                if (e.target.id === 'buscadorProcesos') {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        buscarProcesos(1);
                    }, 500);
                }
            });

            // 🔥 DELEGACIÓN DE EVENTOS - Botones de reabrir proceso
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.reopen-process-btn')) {
                    const btn = e.target.closest('.reopen-process-btn');
                    const procesoId = btn.getAttribute('data-id');
                    // Aquí va tu lógica de reabrir proceso
                    console.log('Reabrir proceso:', procesoId);
                }
            });
        })();
    </script>
</div>
