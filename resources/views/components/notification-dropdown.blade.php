<div class="notification-wrapper">
    <button class="notification-btn" id="notificationBtn">
        <i class="fas fa-bell"></i>
        @if ($notificaciones->count() > 0)
            <span class="notification-badge">
                {{ $notificaciones->count() }}
            </span>
        @endif
    </button>

    <div class="notification-dropdown" id="notificationDropdown">
        @if ($notificaciones->count() > 0)
            <div class="notification-header">
                <span>Notificaciones</span>
                <span class="notification-count">
                    {{ $notificaciones->count() }}
                </span>
            </div>

            @foreach ($notificaciones as $noti)
                @php
                    // Determinar la URL según el tipo de notificación
                    $url = '#';
                    $markRead = '&mark_read=' . $noti->id;

                    if (isset($noti->data['tipo'])) {
                        switch ($noti->data['tipo']) {
                            case 'bienvenida':
                                $url = route('profile.password.edit') . '?mark_read=' . $noti->id;
                                break;
                            case 'pago':
                                $url =
                                    route('pagos.index') . '?proceso=' . ($noti->data['proceso_id'] ?? '') . $markRead;
                                break;
                            case 'asignacion':
                                $url = route('dashboard.abogado') . '?mark_read=' . $noti->id;
                                break;
                            case 'proceso_nuevo':
                                $url =
                                    route('mis.procesos') . '?proceso=' . ($noti->data['proceso_id'] ?? '') . $markRead;
                                break;
                            case 'vencimiento':
                                $url =
                                    route('mis.procesos') . '?proceso=' . ($noti->data['proceso_id'] ?? '') . $markRead;
                                break;
                            case 'traslado':
                                $url =
                                    route('mis.procesos') . '?proceso=' . ($noti->data['proceso_id'] ?? '') . $markRead;
                                break;
                            default:
                                $url = $noti->data['url'] ?? route('mis.procesos');
                                if (strpos($url, '?') === false) {
                                    $url .= '?mark_read=' . $noti->id;
                                } else {
                                    $url .= $markRead;
                                }
                        }
                    } else {
                        // Compatibilidad con notificaciones antiguas
                        $url = route('pagos.index') . '?proceso=' . ($noti->data['proceso_id'] ?? '') . $markRead;
                    }
                @endphp

                <a href="{{ $url }}" class="notification-item">
                    <div class="notification-content">
                        <strong>{{ $noti->data['titulo'] }}</strong>
                        <br>
                        <small>{{ $noti->data['mensaje'] }}</small>

                        {{-- Mostrar tiempo transcurrido --}}
                        <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">
                            {{ $noti->created_at->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- Botón para marcar todas como leídas --}}
            <div style="padding: 0.5rem; border-top: 1px solid #e2e8f0; text-align: center;">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit"
                        style="background: none; border: none; color: #16a34a; cursor: pointer; font-size: 0.85rem;">
                        <i class="fas fa-check-double"></i> Marcar todas como leídas
                    </button>
                </form>
            </div>
        @else
            <div class="notification-empty">
                <i class="fas fa-bell-slash"></i>
                <p>No tienes notificaciones</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        // Toggle dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });

        // Cerrar al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });

        // Cerrar al hacer clic en una notificación
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                notificationDropdown.classList.remove('show');
            });
        });
    </script>
@endpush
