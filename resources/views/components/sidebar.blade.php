<aside class="sidebar" id="sidebar">
    <div class="profile">
        <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png" style="display: none;">

        <div id="loadingIndicator"
            style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.7); color: white; padding: 10px; border-radius: 5px; z-index: 1000;">
            Subiendo...
        </div>

        <div class="profile-pic profile-pic-clickable" onclick="document.getElementById('fileInput').click();"
            title="Haz clic para cambiar tu foto">
            <img src="{{ Auth::user()->foto_perfil ? asset('storage/' . Auth::user()->foto_perfil) : asset('img/sena-servicio-nacional-de-aprendizaje_1747692754.jpeg') }}"
                id="profileImage" alt="Foto de perfil">
        </div>

        <h3>{{ Auth::user()->name }}</h3>
        <p>{{ Auth::user()->email }}</p>
    </div>

    {{-- MENÚ DE NAVEGACIÓN --}}
    <nav class="nav-menu">
        @php
            $role = Auth::user()->role_id;
            $isAbogado = $role == 2;
        @endphp


        {{-- NUEVO: Mi Perfil --}}
        <a href="{{ route('profile.show') }}" class="nav-item {{ request()->routeIs('profile.show') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i>
            <span>Mi Perfil</span>
        </a>

        {{-- Registrar Proceso (solo abogados) --}}
        @if ($isAbogado)
            <a href="{{ route('legal_processes.create') }}"
                class="nav-item {{ request()->routeIs('legal_processes.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i>
                <span>Registrar Proceso</span>
            </a>
        @endif

        {{-- Mis Procesos --}}
        <a href="{{ route('mis.procesos') }}" class="nav-item {{ request()->routeIs('mis.procesos') ? 'active' : '' }}">
            <i class="fas fa-folder-open"></i>
            <span>Mis Procesos</span>
        </a>

        {{-- Conceptos Jurídicos --}}
        <a href="{{ route('conceptos.create') }}"
            class="nav-item {{ request()->routeIs('conceptos.create') ? 'active' : '' }}">
            <i class="fas fa-file-signature"></i>
            <span>Conceptos Jurídicos</span>
        </a>

        {{-- Pagos --}}
        <a href="{{ route('pagos.index') }}" class="nav-item {{ request()->routeIs('pagos.index') ? 'active' : '' }}">
            <i class="fas fa-dollar-sign"></i>
            <span>Pagos</span>
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="sena-logo">
            <a href="{{ route('dashboard.abogado') }}">
                <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Sena Verde">
            </a>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>