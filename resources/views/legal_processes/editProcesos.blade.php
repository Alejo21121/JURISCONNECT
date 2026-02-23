<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proceso Judicial - CSS Puro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <link rel="stylesheet" href="{{ asset('css/abogado.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/editPro.css') }}">
    @vite(['resources/js/dash.js'])
</head>

<body>
    <div class="dashboard-wrapper">
        <div class="overlay" id="overlay"></div>

        {{-- SIDEBAR REUTILIZABLE --}}
        <x-sidebar />

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="main-content" id="mainContent">

            {{-- Header con hamburguesa y notificaciones --}}
            <header class="header">
                <div class="header-left">
                    <button class="hamburger" id="hamburgerBtn">☰</button>
                    <i class="fas fa-balance-scale" style="color:#28a745; font-size:30px; margin-right:10px;"></i>
                    <h1>Sistema Jurídico</h1>
                </div>
                <div class="header-right">
                    {{-- COMPONENTE DE NOTIFICACIONES --}}
                    <x-notification-dropdown />

                    <!-- Logo SENA -->
                    <a href="{{ route('dashboard.abogado') }}">
                        <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
                    </a>
                </div>
            </header>

            <div class="container">
                <div class="main-card fade-in-up">

                    <div class="card-header">
                        <h1>
                            <i class="fas fa-edit"></i>
                            Editar Proceso Judicial
                        </h1>
                        <a href="{{ route('procesos.index') }}" class="back-btn">
                            <i class="fas fa-arrow-left"></i>
                            Volver
                        </a>
                    </div>

                    <div class="card-body">

                        <form class="form" method="POST" enctype="multipart/form-data"
                            action="{{ route('procesos.update', $proceso->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- ESTADO -->
                            <div class="form-row">

                                @php
                                    $estadosNoSeleccionables = ['Reabierto', 'Pago en trámite', 'Traslado'];
                                @endphp

                                <div class="form-group">
                                    <label class="form-label">Estado de proceso *</label>

                                    @php
                                        $estados = [
                                            'Pendiente',
                                            'Radicado',
                                            'Admisión',
                                            'Trasladar', // este es el disparador
                                            'Traslado', // estado real
                                            'Audiencia',
                                            'Fallo favorable',
                                            'Fallo desfavorable',
                                            'Apelación',
                                            'Ejecutoria',
                                            'Conciliado',
                                            'Archivado',
                                            'Reabierto',
                                            'Pago en trámite',
                                        ];

                                        $tienePagos = $proceso->pago()->exists();

                                        if (Auth::user()->role_id != 2 || $tienePagos) {
                                            $estados = array_filter($estados, fn($e) => $e !== 'Trasladar');
                                        }
                                    @endphp

                                    <select class="form-select" name="estado" id="estadoSelect" required>
                                        @foreach ($estados as $estado)
                                            @php
                                                $esActual = old('estado', $proceso->estado) === $estado;
                                                $esBloqueado = in_array($estado, $estadosNoSeleccionables);
                                            @endphp

                                            @if (!$esBloqueado || $esActual)
                                                <option value="{{ $estado }}" {{ $esActual ? 'selected' : '' }}
                                                    {{ $esBloqueado && !$esActual ? 'disabled' : '' }}>
                                                    {{ $estado }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>

                                </div>
                                @php
                                    $esAbogado = Auth::user()->role_id == 2;
                                @endphp

                                @if ($esAbogado)
                                    <div class="form-group" id="trasladoBox" style="display:none;">
                                        <label class="form-label">Seleccionar abogado destino *</label>
                                        <select class="form-select" name="nuevo_lawyer_id">
                                            <option value="">-- Elegir abogado --</option>
                                            @foreach (\App\Models\Lawyer::where('id', '!=', $proceso->lawyer_id)->get() as $lawyer)
<option value="{{ $lawyer->id }}">
                                        {{ $lawyer->nombre }} {{ $lawyer->apellido }}
                                    </option>
@endforeach
                            </select>
                        </div>
@endif
      
                    </div>

                    <!-- TIPO / RADICADO -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tipo de Proceso *</label>
                            <select class="form-select" name="tipo_proceso" required>
                                @foreach (['Civil', 'Penal', 'Laboral', 'Administrativo', 'Familia', 'Comercial'] as $tipo)
<option value="{{ $tipo }}"
                                        {{ old('tipo_proceso', $proceso->tipo_proceso) == $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
 @endforeach
                                                </select>
                                                </div>

                                                <div class="form-group">
                                                <label class="form-label">Número de Radicado *</label>
                                                <input type="text" class="form-control bg-light"
                                                value="{{ $proceso->numero_radicado }}"
                                                readonly>
                                                <input type="hidden" name="numero_radicado"
                                                value="{{ $proceso->numero_radicado }}">
                                                </div>
                                                </div>

                                                <!-- PARTES -->
                                                <div class="form-row">
                                                <div class="form-group">
                                                <label class="form-label">Demandante *</label>
                                                <input type="text" class="form-control" name="demandante"
                                                value="{{ old('demandante', $proceso->demandante) }}" required>
                                                </div>

                                                <div class="form-group">
                                                <label class="form-label">Demandado *</label>
                                                <input type="text" class="form-control" name="demandado"
                                                value="{{ old('demandado', $proceso->demandado) }}" required>
                                                </div>
                                                </div>

                                                @if ($procesoPagado)
                                                <div class="alert alert-warning" style="margin-bottom:20px;">
                                                <i class="fas fa-lock"></i>
                                                Este proceso ya cuenta con pagos registrados.
                                                <strong>Los datos de pago no pueden ser
                                                modificados.</strong>
                                                </div>
                                            @endif

                                            <div class="form-row">
                                            <div class="form-group">
                                            <label class="form-label">¿Requiere pago? *</label>

                                            @if ($procesoPagado)
                                            <!-- SOLO TEXTO -->
                                            <div class="form-control bg-light" style="pointer-events:none;">
                                            {{ $proceso->requiere_pago ? 'Sí' : 'No' }}
                                            </div>

                                            <!-- Mantener valor para el backend -->
                                            <input type="hidden" name="requiere_pago"
                                            value="{{ $proceso->requiere_pago }}">
                                        @else
                                            <!-- SELECT NORMAL -->
                                            <select class="form-select" name="requiere_pago" id="requiere_pago">
                                            <option value="0"
                                            {{ $proceso->requiere_pago == 0 ? 'selected' : '' }}>No
                                            </option>
                                            <option value="1"
                                            {{ $proceso->requiere_pago == 1 ? 'selected' : '' }}>Sí
                                            </option>
                                            </select>
                                @endif
                                </div>

                                <div class="form-group" id="valor_pago_box" style="display:none;">
                                <label class="form-label">Valor estimado *</label>
                                <input type="text" class="form-control" name="valor_estimado"
                                id="valor_estimado"
                                value="{{ old('valor_estimado', number_format($proceso->valor_estimado, 0, ',', '.')) }}"
                                {{ $procesoPagado ? 'readonly' : '' }}>
                                </div>

                                <!-- FECHA DE VENCIMIENTO -->
                                <div class="form-group">
                                <label class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Fecha de Vencimiento
                                </label>
                                <input
                                type="date"
                                name="fecha_vencimiento"
                                id="fecha_vencimiento"
                                class="form-control"
                                min="{{ date('Y-m-d') }}"
                                value="{{ old('fecha_vencimiento', $proceso->fecha_vencimiento ? $proceso->fecha_vencimiento->format('Y-m-d') : '') }}"
                                >
                                <small class="form-text text-muted">
                                Recibirás notificaciones 7, 3 y 1 día antes del vencimiento.
                                </small>
                                </div>
                                </div>

                                <!-- DESCRIPCIÓN -->
                                <div class="form-group full-width">
                                <label class="form-label">Detalle del caso *</label>
                                <textarea class="form-textarea" name="descripcion" rows="4" required>{{ old('descripcion', $proceso->descripcion) }}</textarea>
                                </div>

                                <!-- DOCUMENTOS EXISTENTES -->
                                @if ($proceso->documentos->count())
                                <hr>
                                <h3 style="margin-bottom:15px;">📎 Documentos del proceso</h3>

                                <div class="documents-list">
                                @foreach ($proceso->documentos as $doc)
                                <div class="doc-item">
                                <div class="doc-info">
                                <i class="fas fa-file-pdf"></i>
                                <span>{{ $doc->nombre }}</span>
                                </div>

                                <div class="doc-actions">
                                <a href="{{ Storage::url($doc->ruta) }}" target="_blank" class="btn-view">
                                <i class="fas fa-eye"></i>
                                </a>

                                <button type="button" class="btn-delete"
                                onclick="eliminarDocumento({{ $doc->id }})">
                                <i class="fas fa-trash"></i>
                                </button>

                                </div>
                                </div>
                                @endforeach
                                </div>
                                @endif

                                <!-- SUBIR NUEVOS DOCUMENTOS -->
                                <div class="form-group full-width">
                                <label class="form-label">
                                <i class="fas fa-file-upload"></i> Subir nuevos documentos
                                </label>

                                <div class="file-input">
                               <input type="file" id="documento" name="documentos[]" multiple>
                                <label for="documento" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Seleccionar archivo</span>
                                </label>
                                </div>

                                <div id="filePreview" class="file-preview"></div>
                                </div>

                                <!-- BOTÓN FINAL -->
                                <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Proceso
                                </button>
                                </div>

                                </form>
                                </div>
                                </div>
                                </div>

                                <!-- Modal eliminar documento -->
                                <div id="modalEliminar" class="modal-overlay">
                                <div class="modal-box">
                                <div class="modal-icon">
                                <i class="fas fa-triangle-exclamation"></i>
                                </div>

                                <h3>¿Eliminar este documento?</h3>
                                <p>Este documento se eliminara de forma inmediata,<br>
                                incluso si no guarda el proceso.</p>

                                <div class="modal-actions">
                                <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                                <button class="btn-confirm"
                                onclick="confirmarEliminar()">Eliminar</button>
                                </div>
                                </div>
                                </div>

                                <script>
                                    window.procesoEstado = "{{ $proceso->estado }}";
                                </script>

                                <script src="{{ asset('js/editPro.js') }}"></script>
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                </body>

                                </html>
                                
