<div class="table-wrapper">
    <table class="lawyers-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Tipo Doc.</th>
                <th>N° Documento</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Abogados Asignados</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assistantsSimple as $assistant)
                <tr>
                    <td>{{ $assistant->nombre }}</td>
                    <td>{{ $assistant->apellido }}</td>
                    <td>{{ $assistant->tipo_documento }}</td>
                    <td>{{ $assistant->numero_documento }}</td>
                    <td>{{ $assistant->correo }}</td>
                    <td>{{ $assistant->telefono ?? 'N/A' }}</td>
                    <td>
                        @if ($assistant->lawyers && $assistant->lawyers->count() > 0)
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($assistant->lawyers as $lawyer)
                                    <li>{{ $lawyer->nombre }} {{ $lawyer->apellido }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #999;">Sin abogados asignados</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        No hay asistentes jurídicos registrados
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
    <!-- Incluir la paginación -->
    @include('profile.partials.pagination', [
        'items' => $assistantsSimple,
        'pageKey' => 'assistantsSimplePage',
    ])

</div>
