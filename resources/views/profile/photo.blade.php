<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="container" style="max-width: 500px; margin: 40px auto;">
        <h2 style="text-align: center;">Cambiar Foto de Perfil</h2>

        @if (session('success'))
            <div style="color: green; margin-top: 15px;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data"
            style="margin-top: 20px;">
            @csrf

            <div class="profile-icon">
                @if (Auth::user()->foto_perfil)
                    <img src="{{ asset('storage/' . Auth::user()->foto_perfil) }}" alt="Foto de perfil"
                        style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                @else
                    <img src="{{ asset('img/silueta-atardecer-foto-perfil.webp') }}" alt="Foto por defecto"
                        style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                @endif
            </div>


            <div class="form-group" style="margin-bottom: 15px;">
                <label for="foto_perfil">Selecciona una imagen:</label>
                <input type="file" name="foto_perfil" id="foto_perfil" required>
                @error('foto_perfil')
                    <div style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn"
                style="background-color: #6abf4b; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;">
                Subir Foto
            </button>
        </form>
    </div>
</x-app-layout>
