<x-app-layout>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"> Seleccionar Proceso para Concepto Jurídico</h3>
                        <a href="{{ route('dashboard.abogado') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                    </div>

                    <div class="card-body">
                        @if ($procesos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th># Radicado</th>
                                            <th>Tipo de Proceso</th>
                                            <th>Demandante</th>
                                            <th>Demandado</th>
                                            <th>Fecha Radicación</th>
                                            <th>Estado Concepto</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($procesos as $proceso)
                                            <tr
                                                class="{{ $proceso->concepto_juridico ? 'table-success' : 'table-warning' }}">
                                                <td><strong>{{ $proceso->numero_radicado }}</strong></td>
                                                <td>{{ $proceso->tipo_proceso }}</td>
                                                <td>{{ $proceso->demandante }}</td>
                                                <td>{{ $proceso->demandado }}</td>
                                                <td>
                                                    {{ $proceso->fecha_radicacion ? $proceso->fecha_radicacion->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td>
                                                    @if ($proceso->concepto_juridico)
                                                        <span class="badge bg-success"> Completado</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"> Pendiente</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('abogado.crear-concepto', $proceso->id) }}"
                                                        class="btn btn-sm {{ $proceso->concepto_juridico ? 'btn-outline-success' : 'btn-outline-primary' }}">
                                                        {{ $proceso->concepto_juridico ? 'Ver / Editar' : 'Crear Concepto' }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i> No tienes procesos asignados actualmente.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
