<div class="table-container">
    <table class="lawyers-table-simple">
        <thead>
            <tr>
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>TIPO DE DOCUMENTO</th>
                <th>NÚMERO DE DOCUMENTO</th>
                <th>CORREO</th>
                <th>TELÉFONO</th>
                <th>ESPECIALIDAD</th>
            </tr>
        </thead>
        <tbody id="tableBodySimple">
            @foreach ($lawyersSimple as $lawyer)
                <tr>
                    <td>{{ $lawyer->nombre }}</td>
                    <td>{{ $lawyer->apellido }}</td>
                    <td>{{ $lawyer->tipo_documento }}</td>
                    <td>{{ $lawyer->numero_documento }}</td>
                    <td>{{ $lawyer->correo }}</td>
                    <td>{{ $lawyer->telefono }}</td>
                    <td>{{ $lawyer->especialidad }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Incluir la paginación -->
    @include('profile.partials.pagination', [
        'items' => $lawyersSimple,
        'pageKey' => 'lawyersSimplePage',
    ])

</div>
