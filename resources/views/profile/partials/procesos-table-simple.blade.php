<div class="table-wrapper">
    <table class="lawyers-table">
        <thead>
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


    @include('profile.partials.pagination', [
        'items' => $procesosSimple,
        'pageKey' => 'procesosSimplePage',
    ])
</div>
