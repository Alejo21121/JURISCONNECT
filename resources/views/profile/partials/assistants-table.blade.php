<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    <div class="table-container table-wrapper">
        <table class="lawyers-table">
            <thead>
                @php
                    $sortA = request('sort_assistant', 'id');
                    $dirA = request('dir_assistant', 'asc');

                    function thSortAssistant($col, $label, $sort, $dir)
                    {
                        $newDir = $sort === $col && $dir === 'asc' ? 'desc' : 'asc';
                        $url = request()->fullUrlWithQuery(['sort_assistant' => $col, 'dir_assistant' => $newDir]);
                        $active = $sort === $col;
                        $arrow = $active ? ($dir === 'asc' ? '↑' : '↓') : '↕';
                        $class = 'th-sort' . ($active ? ' th-active' : '');
                        return "<th class=\"$class\">
                <a href=\"$url\" class=\"th-link\">
                    <span>$label</span>
                    <span class=\"sort-arrow\" style=\"font-size:15px;\">$arrow</span>
                </a>
            </th>";
                    }
                @endphp
                <tr>
                    {!! thSortAssistant('nombre', 'Nombre', $sortA, $dirA) !!}
                    {!! thSortAssistant('apellido', 'Apellido', $sortA, $dirA) !!}
                    {!! thSortAssistant('tipo_documento', 'Tipo Doc.', $sortA, $dirA) !!}
                    {!! thSortAssistant('numero_documento', 'N° Documento', $sortA, $dirA) !!}
                    {!! thSortAssistant('correo', 'Correo', $sortA, $dirA) !!}
                    {!! thSortAssistant('telefono', 'Teléfono', $sortA, $dirA) !!}
                    <th>Abogados Asignados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="assistantsTableBody">
                @forelse($assistants as $assistant)
                    <tr data-assistant-id="{{ $assistant->id }}">
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
                        <td>
                            <div class="action-buttons">
                                <button class="btn-edit-assistant" data-id="{{ $assistant->id }}"
                                    data-nombre="{{ $assistant->nombre }}" data-apellido="{{ $assistant->apellido }}"
                                    data-tipo_documento="{{ $assistant->tipo_documento }}"
                                    data-numero_documento="{{ $assistant->numero_documento }}"
                                    data-correo="{{ $assistant->correo }}" data-telefono="{{ $assistant->telefono }}"
                                    data-lawyers='@json($assistant->lawyers->pluck('id'))'>
                                    Editar
                                </button>
                                <form action="{{ route('asistentes.destroy', $assistant->id) }}" method="POST"
                                    class="delete-assistant-form"
                                    data-name="{{ $assistant->nombre }} {{ $assistant->apellido }}"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">
                            No hay asistentes jurídicos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Incluir la paginación -->
        @include('profile.partials.pagination', [
            'items' => $assistants,
            'pageKey' => 'assistantsPage',
        ])

    </div>

    <!-- Mensajes de sesión para alertas -->
    @if (session('success'))
        <div data-success-message="{{ session('success') }}" style="display: none;"></div>
    @endif

    @if (session('update'))
        <div data-update-message="{{ session('update') }}" style="display: none;"></div>
    @endif

    @if (session('delete'))
        <div data-delete-message="{{ session('delete') }}" style="display: none;"></div>
    @endif

    @if (session('error'))
        <div data-error-message="{{ session('error') }}" style="display: none;"></div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/asistentes.js') }}" defer></script>

</body>

</html>
