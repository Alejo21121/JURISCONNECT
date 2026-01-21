<div class="table-wrapper">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <table class="lawyers-table" id="tablaProcesos">
        <thead>
            <tr>
                <th colspan="6">
                    <div style="position: relative; margin: 0 auto;">
                        <i class="fas fa-search"
                            style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none; z-index: 1;"></i>

                        <input type="text" id="buscadorProcesos" class="input-buscador" placeholder="Buscar proceso"
                            style="padding-left: 40px; width: 100%;">
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
        <tbody>
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

    <script>
        document.getElementById('buscadorProcesos').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            const filas = document.querySelectorAll('#tablaProcesos tbody tr');

            filas.forEach(fila => {
                const textoFila = fila.innerText.toLowerCase();

                if (textoFila.includes(filtro)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    </script>


    @include('profile.partials.pagination', [
        'items' => $procesosSimple,
        'pageKey' => 'procesosSimplePage',
    ])
</div>
