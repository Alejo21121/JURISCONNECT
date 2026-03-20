<div class="table-container">
    <table>
        <thead>
            @php
                $sortL = request('sort_lawyer', 'id');
                $dirL = request('dir_lawyer', 'asc');

                function thSortLawyer($col, $label, $sort, $dir)
                {
                    $newDir = $sort === $col && $dir === 'asc' ? 'desc' : 'asc';
                    $url = request()->fullUrlWithQuery(['sort_lawyer' => $col, 'dir_lawyer' => $newDir]);
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
                {!! thSortLawyer('nombre', 'Nombre', $sortL, $dirL) !!}
                {!! thSortLawyer('apellido', 'Apellido', $sortL, $dirL) !!}
                {!! thSortLawyer('tipo_documento', 'Tipo Documento', $sortL, $dirL) !!}
                {!! thSortLawyer('numero_documento', 'Nº Documento', $sortL, $dirL) !!}
                {!! thSortLawyer('correo', 'Correo', $sortL, $dirL) !!}
                {!! thSortLawyer('telefono', 'Teléfono', $sortL, $dirL) !!}
                {!! thSortLawyer('especialidad', 'Especialidad', $sortL, $dirL) !!}
                @if ($lawyers->contains(fn($l) => $l->user && $l->user->role_id == 4))
                    <th>Supervisor</th>
                @endif
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @foreach ($lawyers ?? [] as $lawyer)
                <tr data-id="{{ $lawyer->id }}">
                    <td>{{ $lawyer->nombre }}</td>
                    <td>{{ $lawyer->apellido }}</td>
                    <td>{{ $lawyer->tipo_documento }}</td>
                    <td>{{ $lawyer->numero_documento }}</td>
                    <td>{{ $lawyer->correo }}</td>
                    <td>{{ $lawyer->telefono }}</td>
                    <td>{{ $lawyer->especialidad }}</td>
                    @if (
                        $lawyers->contains(function ($lawyer) {
                            return $lawyer->user && $lawyer->user->role_id == 4;
                        }))
                        <td>
                            @if ($lawyer->user && $lawyer->user->role_id == 4)
                                <span class="badge-supervisor">Supervisor</span>
                            @endif
                        </td>
                    @endif
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" data-id="{{ $lawyer->id }}" data-nombre="{{ $lawyer->nombre }}"
                                data-apellido="{{ $lawyer->apellido }}"
                                data-tipo_documento="{{ $lawyer->tipo_documento }}"
                                data-numero_documento="{{ $lawyer->numero_documento }}"
                                data-correo="{{ $lawyer->correo }}" data-telefono="{{ $lawyer->telefono }}"
                                data-especialidad="{{ $lawyer->especialidad }}">
                                Editar
                            </button>

                            <form action="{{ route('lawyers.destroy', $lawyer->id) }}" method="POST"
                                class="delete-lawyer-form" data-id="{{ $lawyer->id }}"
                                data-name="{{ $lawyer->nombre }} {{ $lawyer->apellido }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @include('profile.partials.pagination', ['items' => $lawyers, 'pageKey' => 'page'])
</div>

<script>
    // ==========================================
    // BUSCADOR REAL AJAX PARA ABOGADOS
    // ==========================================

    // ── Tabla abogados sin recarga ──
    const stateLawyers = {
        search: '',
        sort_lawyer: new URLSearchParams(window.location.search).get('sort_lawyer') || 'id',
        dir_lawyer: new URLSearchParams(window.location.search).get('dir_lawyer') || 'asc',
        page: 1,
    };

    function cargarTablaAbogados() {
        const container = document.getElementById('AbogadosTableWrapper');
        if (!container) return;

        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';

        const params = new URLSearchParams({
            search: stateLawyers.search,
            sort_lawyer: stateLawyers.sort_lawyer,
            dir_lawyer: stateLawyers.dir_lawyer,
            lawyersPage: stateLawyers.page,
            section: 'lawyers',
        });

        fetch(`/dashboard?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.html;
                    container.style.opacity = '1';
                    container.style.pointerEvents = '';
                    bindLawyerEvents();
                }
            })
            .catch(() => {
                container.style.opacity = '1';
                container.style.pointerEvents = '';
            });
    }

    function bindLawyerEvents() {
        // Ordenamiento
        document.querySelectorAll('#AbogadosTableWrapper .th-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                stateLawyers.sort_lawyer = url.searchParams.get('sort_lawyer');
                stateLawyers.dir_lawyer = url.searchParams.get('dir_lawyer');
                stateLawyers.page = 1;
                cargarTablaAbogados();
            });
        });

        // Paginación
        document.querySelectorAll('#AbogadosTableWrapper .pagination-btn[href]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                stateLawyers.page = url.searchParams.get('lawyersPage') || 1;
                cargarTablaAbogados();
            });
        });
    }

    // Buscador abogados
    const searchAbogados = document.getElementById('searchAbogados');
    if (searchAbogados) {
        let timer;
        searchAbogados.addEventListener('input', function() {
            clearTimeout(timer);
            stateLawyers.search = this.value;
            stateLawyers.page = 1;
            timer = setTimeout(() => cargarTablaAbogados(), 350);
        });
    }

    document.addEventListener('DOMContentLoaded', () => bindLawyerEvents());
</script>
