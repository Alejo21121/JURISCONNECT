@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Proceso Legal</h4>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('procesos.update', $proceso->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control @error('titulo') is-invalid @enderror"
                                    id="titulo" name="titulo" value="{{ old('titulo', $proceso->titulo) }}" required>
                                @error('titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label"> *</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion"
                                    rows="4" required>{{ old('descripcion', $proceso->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tipo_proceso" class="form-label">Tipo de Proceso *</label>
                                <select class="form-control @error('tipo_proceso') is-invalid @enderror" id="tipo_proceso"
                                    name="tipo_proceso" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="civil"
                                        {{ old('tipo_proceso', $proceso->tipo_proceso) == 'civil' ? 'selected' : '' }}>Civil
                                    </option>
                                    <option value="penal"
                                        {{ old('tipo_proceso', $proceso->tipo_proceso) == 'penal' ? 'selected' : '' }}>Penal
                                    </option>
                                    <option value="laboral"
                                        {{ old('tipo_proceso', $proceso->tipo_proceso) == 'laboral' ? 'selected' : '' }}>
                                        Laboral</option>
                                    <option value="administrativo"
                                        {{ old('tipo_proceso', $proceso->tipo_proceso) == 'administrativo' ? 'selected' : '' }}>
                                        Administrativo</option>
                                </select>
                                @error('tipo_proceso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-control @error('estado') is-invalid @enderror" id="estado"
                                    name="estado" required>
                                    <select name="estado" required>
                                        <option value="activo" {{ $proceso->estado == 'activo' ? 'selected' : '' }}>Activo
                                        </option>
                                        <option value="en_proceso"
                                            {{ $proceso->estado == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="suspendido"
                                            {{ $proceso->estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                        <option value="terminado" {{ $proceso->estado == 'terminado' ? 'selected' : '' }}>
                                            Terminado</option>
                                        <option value="archivado" {{ $proceso->estado == 'archivado' ? 'selected' : '' }}>
                                            Archivado</option>
                                    </select>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror"
                                    id="fecha_inicio" name="fecha_inicio"
                                    value="{{ old('fecha_inicio', $proceso->fecha_inicio) }}" required>
                                @error('fecha_inicio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('procesos.show', $proceso->id) }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Actualizar Proceso
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
