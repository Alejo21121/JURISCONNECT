<x-app-layout>
    <x-slot name="header">
    </x-slot>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <div class="dashboard-wrapper">

        <div class="overlay" id="overlay"></div>

        <!-- MODAL PARA CREAR ABOGADO -->
        <div class="modal" id="createLawyerModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Crear Nuevo Abogado</h2>
                    <button class="modal-close" id="closeModal">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('lawyers.store') }}" method="POST" onsubmit="saveCurrentSection()">
                        @csrf
                        <input type="hidden" name="tipodeusuario" value="lawyer">

                        <div class="form-group">
                            <label for="nombre_lawyer">Nombre:</label>
                            <input type="text" id="nombre_lawyer" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="apellido_lawyer">Apellido:</label>
                            <input type="text" id="apellido_lawyer" name="apellido" required>
                        </div>

                        <div class="form-group">
                            <label for="tipoDocumento_lawyer">Tipo de Documento:</label>
                            <select id="tipoDocumento_lawyer" name="tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PAS">Pasaporte</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="numeroDocumento_lawyer">Número de Documento:</label>
                            <input type="text" id="numeroDocumento_lawyer" name="numero_documento" required>
                        </div>

                        <div class="form-group">
                            <label for="correo_lawyer">Correo Electrónico:</label>
                            <input type="email" id="correo_lawyer" name="correo" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono_lawyer">Teléfono:</label>
                            <input type="tel" id="telefono_lawyer" name="telefono">
                        </div>

                        <div class="form-group">
                            <label for="especialidad_lawyer">Especialidad:</label>
                            <input type="text" id="especialidad_lawyer" name="especialidad">
                        </div>

                        <div class="form-group">
                            <label for="rol_lawyer">Tipo de Abogado:</label>
                            <select id="rol_lawyer" name="role_id" required>
                                <option value="2">Abogado</option>
                                <option value="4">Abogado Supervisor</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" id="cancelBtn">Cancelar</button>
                            <button type="submit" class="btn-submit">Crear Abogado</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL PARA CREAR ASISTENTE -->
        <div class="modal" id="modalAsistente">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Crear Nuevo Asistente</h2>
                    <button class="modal-close" id="closeAsistente">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('lawyers.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipodeusuario" value="assistant">

                        <div class="form-group">
                            <label for="nombre_assistant">Nombre:</label>
                            <input type="text" id="nombre_assistant" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="apellido_assistant">Apellido:</label>
                            <input type="text" id="apellido_assistant" name="apellido" required>
                        </div>

                        <div class="form-group">
                            <label for="tipoDocumento_assistant">Tipo de Documento:</label>
                            <select id="tipoDocumento_assistant" name="tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PAS">Pasaporte</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="numeroDocumento_assistant">Número de Documento:</label>
                            <input type="text" id="numeroDocumento_assistant" name="numero_documento" required>
                        </div>

                        <div class="form-group">
                            <label for="correo_assistant">Correo Electrónico:</label>
                            <input type="email" id="correo_assistant" name="correo" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono_assistant">Teléfono:</label>
                            <input type="tel" id="telefono_assistant" name="telefono">
                        </div>

                        <!-- Contenedor dinámico -->
                        <div class="form-group" id="lawyerSelectContainer">
                            <label>Abogados asignados:</label>
                            <div id="lawyerList"></div>

                            <button type="button" class="btn-submit" style="margin-top:10px;" id="addLawyerBtn">
                                + Agregar Abogado
                            </button>
                        </div>

                        <!-- SELECT base oculto para clonar -->
                        <select class="lawyer-select" id=".lawyer-select" style="display:none;">
                            <option value="">Seleccione abogado...</option>
                            @foreach ($abogados as $lawyer)
                                <option value="{{ $lawyer->id }}">
                                    {{ $lawyer->nombre }} {{ $lawyer->apellido }}
                                </option>
                            @endforeach
                        </select>


                        <div class="form-actions">
                            <button type="button" class="btn-cancel" id="cancelAsistente">Cancelar</button>
                            <button type="submit" class="btn-submit">Crear Asistente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal para editar abogado -->
        <div class="modal" id="editLawyerModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Editar Abogada</h2>
                    <button class="modal-close" id="closeEditModal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="editLawyerForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="editNombre">Nombre:</label>
                            <input type="text" id="editNombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="editApellido">Apellido:</label>
                            <input type="text" id="editApellido" name="apellido" required>
                        </div>

                        <div class="form-group">
                            <label for="editTipoDocumento">Tipo de Documento:</label>
                            <select id="editTipoDocumento" name="tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PAS">Pasaporte</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editNumeroDocumento">Número de Documento:</label>
                            <input id="editNumeroDocumento" name="numero_documento" required>
                        </div>

                        <div class="form-group">
                            <label for="editCorreo">Correo:</label>
                            <input type="email" id="editCorreo" name="correo" required>
                        </div>

                        <div class="form-group">
                            <label for="editTelefono">Teléfono:</label>
                            <input type="tel" id="editTelefono" name="telefono">
                        </div>

                        <div class="form-group">
                            <label for="editEspecialidad">Especialidad:</label>
                            <input type="text" id="editEspecialidad" name="especialidad">
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" id="cancelEditBtn">Cancelar</button>
                            <button type="submit" class="btn-submit">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para editar asistente -->
        <div class="modal" id="editAssistantModal">
            <div class="modal-content-asistente">
                <div class="modal-header">
                    <h2>Editar Asistente</h2>
                    <button class="modal-close" id="closeEditAssistantModal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editAssistantForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="editNombre">Nombre:</label>
                            <input type="text" id="editAssistantNombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="editApellido">Apellido:</label>
                            <input type="text" id="editAssistantApellido" name="apellido" required>
                        </div>

                        <div class="form-group">
                            <label for="editTipoDocumento">Tipo de Documento:</label>
                            <select id="editAssistantTipoDocumento" name="tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PAS">Pasaporte</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editNumeroDocumento">Número de Documento:</label>
                            <input type="text" id="editAssistantNumeroDocumento" name="numero_documento" required>
                        </div>


                        <div class="form-group">
                            <label for="editCorreo">Correo:</label>
                            <input type="email" id="editAssistantCorreo" name="correo" required>
                        </div>


                        <div class="form-group">
                            <label for="editTelefono">Teléfono:</label>
                            <input type="tel" id="editAssistantTelefono" name="telefono">
                        </div>

                        <select class="lawyer-select" style="display:none;">
                            <option value="">Seleccione abogado...</option>
                            @foreach ($abogados as $lawyer)
                                <option value="{{ $lawyer->id }}">
                                    {{ $lawyer->nombre }} {{ $lawyer->apellido }}
                                </option>
                            @endforeach
                        </select>

                        <div class="form-group">
                            <label>Abogados asignados:</label>
                            <div id="assignedLawyersContainer"></div>
                            <button type="button" class="btn-submit" style="margin-top:10px;" id="addLawyerBtn">+
                                Agregar abogado</button>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" id="cancelEditBtn">Cancelar</button>
                            <button type="submit" class="btn-submit">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="profile">
                <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png" style="display: none;">

                <div id="loadingIndicator"
                    style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); color: white; padding: 10px; border-radius: 5px; z-index: 1000;">
                    Subiendo...
                </div>

                <div class="profile-pic" onclick="document.getElementById('fileInput').click();"
                    style="cursor: pointer; position: relative;" title="Haz clic para cambiar tu foto">
                    <img src="{{ Auth::user()->foto_perfil ? asset('storage/' . Auth::user()->foto_perfil) : asset('img/sena-servicio-nacional-de-aprendizaje_1747692754.jpeg') }}"
                        id="profileImage" alt="Foto de perfil">
                </div>
                <h3>{{ Auth::user()->name }}</h3>
                <p>{{ Auth::user()->email }}</p>
            </div>

            <div class="nav-menu">

                <button class="nav-btn" data-section="perfil">Mi Perfil</button>

                <button class="nav-btn active" data-section="dashboard">
                    Dashboard
                </button>

                <button class="nav-btn" data-section="lawyers">
                    Gestión de Abogados
                </button>

                <button class="nav-btn" data-section="assistants">
                    Gestión de Asistentes
                </button>
            </div>

            <div class="sena-logo">
                <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo SENA">
            </div>

            <!-- Botón de Cerrar Sesión -->
            <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                @csrf
                <button type="submit" class="logout-btn">
                    Cerrar Sesión
                </button>
            </form>
        </div>

        <!-- Contenido Principal -->
        <div class="main-content" id="mainContent">
            <div class="header">
                <button class="hamburger" id="hamburgerBtn">☰</button>
                <div class="title-logo-container">
                    <h1 class="title">JustConnect SENA</h1>
                </div>
                <div class="logo-container">
                    <img src="{{ asset('img/LogoSena_Verde.png') }}" alt="Logo Empresa" class="logo">
                </div>
            </div>

            <div class="content-panel">

                <!-- SECCIÓN DASHBOARD PRINCIPAL -->
                <div class="section-content active" id="dashboard-section">

                    <section class="admin-hero">
                        <div class="admin-hero-circles">
                            <div class="admin-circle admin-circle--1"></div>
                            <div class="admin-circle admin-circle--2"></div>
                        </div>
                        <div class="admin-hero-content">
                            <span class="admin-hero-tag">
                                <span class="admin-hero-dot"></span>
                                Sistema activo
                            </span>
                            <h1 class="admin-hero-title">
                                Dashboard <span class="admin-hero-accent">Principal</span>
                            </h1>
                            <p class="admin-hero-sub">Resumen general del sistema JustConnect SENA</p>
                        </div>
                    </section>

                    <div class="admin-stats-grid">

                        <div class="admin-stat-card" id="lawyersStatCard" style="cursor:pointer;">
                            <div class="admin-stat-visual admin-stat-visual--lawyers">
                                <div class="admin-stat-icon">👨‍⚖️</div>
                            </div>
                            <div class="admin-stat-body">
                                <p class="admin-stat-cat">Usuarios</p>
                                <h3 class="admin-stat-num">{{ $totalLawyers }}</h3>
                                <p class="admin-stat-label">Abogados Registrados</p>
                                <span class="admin-stat-link">Ver detalle →</span>
                            </div>
                        </div>

                        <div class="admin-stat-card" id="casesStatCard" style="cursor:pointer;">
                            <div class="admin-stat-visual admin-stat-visual--cases">
                                <div class="admin-stat-icon">📋</div>
                            </div>
                            <div class="admin-stat-body">
                                <p class="admin-stat-cat">Expedientes</p>
                                <h3 class="admin-stat-num">{{ $cases_count }}</h3>
                                <p class="admin-stat-label">Procesos Judiciales</p>
                                <span class="admin-stat-link">Ver detalle →</span>
                            </div>
                        </div>

                        <div class="admin-stat-card" id="assistantsStatCard" style="cursor:pointer;">
                            <div class="admin-stat-visual admin-stat-visual--assistants">
                                <div class="admin-stat-icon">👨‍💼</div>
                            </div>
                            <div class="admin-stat-body">
                                <p class="admin-stat-cat">Usuarios</p>
                                <h3 class="admin-stat-num">{{ $totalAsistentes }}</h3>
                                <p class="admin-stat-label">Asistentes Jurídicos</p>
                                <span class="admin-stat-link">Ver detalle →</span>
                            </div>
                        </div>

                    </div>

                    {{-- TABLAS OCULTAS--}}
                    <div id="lawyersTableWrapper" style="display:none; margin-top:30px;">
                        @include('profile.partials.lawyers-table-simple', ['lawyers' => $lawyers])
                    </div>
                    <div id="assistantsTableWrapper" style="display:none; margin-top:30px;">
                        @include('profile.partials.assistants-table-simple', ['assistants' => $assistants])
                    </div>
                    <div id="procesosTableWrapper" style="display:none; margin-top:30px;">
                        @include('profile.partials.procesos-table-simple', [
                            'procesosSimple' => $procesosSimple,
                        ])
                    </div>

                    {{-- SECCIÓN CÓMO USAR --}}
                    <div class="admin-section-heading">
                        <p class="admin-section-label">Acciones rápidas</p>
                        <h2 class="admin-section-title">¿Qué puedes gestionar?</h2>
                    </div>

                    <div class="admin-actions-grid">
                        <div class="admin-action-card">
                            <div class="admin-action-num">01</div>
                            <h4 class="admin-action-title">Gestionar Abogados</h4>
                            <p class="admin-action-desc">Crea, edita y administra los abogados y supervisores
                                registrados en el sistema.</p>
                        </div>
                        <div class="admin-action-card">
                            <div class="admin-action-num">02</div>
                            <h4 class="admin-action-title">Gestionar Asistentes</h4>
                            <p class="admin-action-desc">Administra los asistentes jurídicos y sus asignaciones a
                                abogados.</p>
                        </div>
                        <div class="admin-action-card">
                            <div class="admin-action-num">03</div>
                            <h4 class="admin-action-title">Ver Procesos</h4>
                            <p class="admin-action-desc">Consulta todos los procesos judiciales registrados en el
                                sistema.</p>
                        </div>
                        <div class="admin-action-card">
                            <div class="admin-action-num">04</div>
                            <h4 class="admin-action-title">Exportar Reportes</h4>
                            <p class="admin-action-desc">Descarga reportes en Excel o PDF de abogados, asistentes y
                                procesos.</p>
                        </div>
                    </div>

                </div>

                {{-- MODAL REABRIR PROCESO --}}
                <div id="reabrirOverlay" class="modal-overlay hidden"></div>

                <div id="reabrirModal" class="modal-alert hidden">
                    <div class="modal-icon">⚠️</div>

                    <h3>Reabrir proceso</h3>

                    <p>
                        ¿Está seguro de reabrir este proceso?<br>
                        El abogado podrá editarlo nuevamente.
                    </p>

                    <div class="modal-actions">
                        <button id="cancelReabrir" class="btn-cancel">Cancelar</button>
                        <button id="confirmReabrir" class="btn-danger">Reabrir</button>
                    </div>
                </div>

                <!-- SECCIÓN GESTIÓN DE ASISTENTES JURÍDICOS -->
                <div class="section-content" id="assistants-section">
                    <div class="section-header">
                        <h2>Gestión de Asistentes Jurídicos</h2>
                        <p>Administrar el registro de asistentes jurídicos del sistema</p>
                    </div>

                    <div class="search-section">
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Buscar por nombre, apellido o número de documento">
                    </div>


                    <div class="action-buttons">
                        <button class="btn-primary" id="btnOpenAsistente">CREAR NUEVO ASISTENTE</button>
                        <a href="{{ route('asistente.export.excel') }}" class="btn-success">EXPORTAR EXCEL</a>
                        <a href="{{ route('asistente.export.pdf') }}" class="btn-danger">EXPORTAR PDF</a>
                    </div>
                    <div id="assistantsTableContainer">
                        @include('profile.partials.assistants-table', ['assistants' => $assistants])
                    </div>
                </div>

                <!-- SECCIÓN GESTIÓN DE ABOGADOS -->
                <div class="section-content" id="lawyers-section">
                    <div class="section-header">
                        <h2>Gestión de Abogados</h2>
                        <p>Administrar el registro de abogados del sistema</p>
                    </div>

                    <div class="search-section">
                        <input type="text" id="searchAbogados" class="search-input"
                            placeholder="Buscar por nombre, apellido o número de documento">
                    </div>

                    <div class="action-buttons">
                        <button class="btn-primary" id="createBtn">CREAR NUEVO ABOGADO</button>
                        <a href="{{ route('lawyers.export.excel') }}" class="btn-success">EXPORTAR EXCEL</a>
                        <a href="{{ route('lawyers.export.pdf') }}" class="btn-danger">EXPORTAR PDF</a>
                    </div>

                    <div id="AbogadosTableWrapper">
                        @include('profile.partials.lawyers-table', ['lawyers' => $lawyers])
                    </div>

                </div>

                <!-- SECCIÓN MI PERFIL -->
                <div class="section-content" id="perfil-section">
                    <div class="section-header">
                        <h2>Mi Perfil</h2>
                        <p>Información personal del administrador</p>
                    </div>

                    <div class="profile-card">
                        <div class="profile-info">
                            <div class="profile-image">
                                <img src="{{ Auth::user()->foto_perfil
                                    ? asset('storage/' . Auth::user()->foto_perfil)
                                    : asset('img/sena-servicio-nacional-de-aprendizaje_1747692754.jpeg') }}"
                                    alt="Foto de perfil">
                            </div>

                            <div class="profile-details">
                                <div class="info-item">
                                    <label>Nombre</label>
                                    <input type="text" value="{{ Auth::user()->name }}" disabled>
                                </div>

                                <div class="info-item">
                                    <label>Correo</label>
                                    <input type="email" value="{{ Auth::user()->email }}" disabled>
                                </div>

                                <div class="info-item">
                                    <label>Rol</label>
                                    <input type="text" value="Administrador" disabled>
                                </div>

                                <div style="margin-top:20px;">
                                    <button class="btn-primary nav-btn" data-section="password">
                                        Cambiar Contraseña
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN CAMBIAR CONTRASEÑA -->
                <div class="section-content" id="password-section">
                    <div class="section-header">
                        <h2>Cambiar Contraseña</h2>
                        <p>Actualiza tu contraseña de administrador</p>
                    </div>

                    <div class="profile-card">
                        <form method="POST" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Contraseña Actual -->
                            <div class="form-group password-group">
                                <label>Contraseña Actual</label>
                                <div class="password-wrapper">
                                    <input type="password" name="current_password" required>
                                    <span class="toggle-password">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Nueva Contraseña -->
                            <div class="form-group password-group">
                                <label>Nueva Contraseña</label>
                                <div class="password-wrapper">
                                    <input type="password" name="password" required>
                                    <span class="toggle-password">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Confirmar -->
                            <div class="form-group password-group">
                                <label>Confirmar Nueva Contraseña</label>
                                <div class="password-wrapper">
                                    <input type="password" name="password_confirmation" required>
                                    <span class="toggle-password">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <div style="margin-top:20px;">
                                <button type="submit" class="btn-primary">
                                    Guardar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
    </div>

    <script src="{{ asset('js/proTable.js') }}"></script>

    @if (session('password_updated'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Contraseña Actualizada!',
                html: `
            <p>Tu contraseña fue cambiada correctamente.</p>
            <p style="color:#2563eb;font-weight:600;">
                Debes volver a iniciar sesión.
            </p>
        `,
                confirmButtonText: 'Aceptar e Iniciar Sesión',
                confirmButtonColor: '#2563eb',
                allowOutsideClick: false
            }).then(() => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('logout') }}";

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = "{{ csrf_token() }}";

                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            });
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            document.querySelectorAll(".toggle-password").forEach(function(toggle) {

                toggle.addEventListener("click", function() {

                    const input = this.parentElement.querySelector("input");
                    const icon = this.querySelector("i");

                    if (input.type === "password") {
                        input.type = "text";
                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");
                    } else {
                        input.type = "password";
                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");
                    }

                });

            });

        });
    </script>


</x-app-layout>
