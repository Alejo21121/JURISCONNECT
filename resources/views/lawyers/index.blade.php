<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lista de Abogados
        </h2>
    </x-slot>

    <div class="py-4 px-6">
        <!-- BUSCADOR -->
        <div class="mb-4">
            <input id="lawyerSearch" type="text" class="w-full p-2 border rounded"
                placeholder="Buscar por nombre, apellido o correo...">
        </div>

        <table id="lawyersTable" class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Nombre</th>
                    <th class="px-4 py-2 border-b">Correo</th>
                    <th class="px-4 py-2 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody id="lawyersTbody">
                @foreach ($lawyers as $lawyer)
                    <tr id="lawyer-{{ $lawyer->id }}"
                        data-name="{{ strtolower($lawyer->nombre . ' ' . $lawyer->apellido) }}"
                        data-email="{{ strtolower($lawyer->user->email ?? '') }}">
                        <td class="px-4 py-2 border-b">{{ $lawyer->nombre }} {{ $lawyer->apellido }}</td>
                        <td class="px-4 py-2 border-b">{{ $lawyer->user->email ?? 'Sin usuario' }}</td>
                        <td class="px-4 py-2 border-b text-center">
                            <button onclick="deleteLawyer({{ $lawyer->id }})"
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Debounce helper
        function debounce(fn, wait = 250) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        // Filtrar filas en cliente
        document.getElementById('lawyerSearch').addEventListener('input', debounce(function(e) {
            const q = (e.target.value || '').trim().toLowerCase();
            const rows = document.querySelectorAll('#lawyersTbody tr');

            if (!q) {
                rows.forEach(r => r.style.display = '');
                return;
            }

            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const match = name.includes(q) || email.includes(q);
                row.style.display = match ? '' : 'none';
            });
        }, 150));

        // Delete via fetch
        function deleteLawyer(id) {
            if (!confirm('¿Seguro que deseas eliminar este abogado?')) return;

            fetch(`/lawyers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => {
                    if (res.ok) {
                        document.getElementById(`lawyer-${id}`).remove();
                    } else {
                        res.json().then(j => {
                            alert(j.message || 'No se pudo eliminar el abogado.');
                        }).catch(() => alert('No se pudo eliminar el abogado.'));
                    }
                })
                .catch(() => alert('Error al eliminar el abogado.'));
        }
    </script>
</x-app-layout>
