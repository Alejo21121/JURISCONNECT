<x-app-layout>
    <!-- Página para el dashboard de los administradores -->
    <x-slot name="header">
        <!-- Header vacío para evitar conflictos -->
    </x-slot>
    <!-- Meta tag para CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Enlace a CSS corregido -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- Contenido sin contenedores restrictivos -->
    <div class="dashboard-wrapper">

        <!-- Overlay para móviles -->
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

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="profile">
                <!-- Input file oculto para la foto de perfil -->
                <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png" style="display: none;">

                <!-- Indicador de carga (oculto por defecto) -->
                <div id="loadingIndicator"
                    style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); color: white; padding: 10px; border-radius: 5px; z-index: 1000;">
                    Subiendo...
                </div>

                <!-- Contenedor de la foto de perfil -->
                <div class="profile-pic" onclick="document.getElementById('fileInput').click();"
                    style="cursor: pointer; position: relative;" title="Haz clic para cambiar tu foto">
                    <img src="{{ Auth::user()->foto_perfil ? asset('storage/' . Auth::user()->foto_perfil) : asset('img/silueta-atardecer-foto-perfil.webp') }}"
                        id="profileImage" alt="Foto de perfil">
                </div>
                <h3>{{ Auth::user()->name }}</h3>
                <p>{{ Auth::user()->email }}</p>
            </div>

            <div class="nav-menu">
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
                    <div class="section-header">
                        <h2>Dashboard Principal</h2>
                        <p>Resumen general del sistema JustConnect SENA</p>
                    </div>

                    <!-- En la sección DASHBOARD PRINCIPAL -->
                    <div class="dashboard-stats">
                        <div class="stat-card" id="lawyersStatCard" style="cursor: pointer;">
                            <div class="stat-icon">👨‍⚖️</div>
                            <div class="stat-info">
                                <h3>{{ $totalLawyers }}</h3>
                                <p>Abogados Registrados</p>
                            </div>
                        </div>

                        <div class="stat-card" id="casesStatCard" style="cursor: pointer;">
                            <div class="stat-icon">📋</div>
                            <div class="stat-info">
                                <h3>{{ $cases_count }}</h3>
                                <p>Procesos Judiciales</p>
                            </div>
                        </div>

                        <div class="stat-card" id="assistantsStatCard" style="cursor: pointer;">
                            <div class="stat-icon">👨‍💼</div>
                            <div class="stat-info">
                                <h3>{{ $totalAsistentes }}</h3>
                                <p>Asistentes Juridicos</p>
                            </div>
                        </div>
                    </div>
                    <!--  TABLA OCULTA INICIALMENTE -->
                    <div id="lawyersTableWrapper" style="display: none; margin-top: 30px;">
                        @include('profile.partials.lawyers-table-simple', ['lawyers' => $lawyers])
                    </div>

                    <!-- 🔽🔽🔽 TABLA DE ASISTENTES OCULTA INICIALMENTE 🔽🔽🔽 -->
                    <div id="assistantsTableWrapper" style="display: none; margin-top: 30px;">
                        @include('profile.partials.assistants-table-simple', ['assistants' => $assistants])
                    </div>

                    <!-- 🔽🔽🔽 TABLA DE PROCESOS OCULTA INICIALMENTE 🔽🔽🔽 -->
                    <div id="procesosTableWrapper" style="display:none; margin-top:30px;">
                        @include('profile.partials.procesos-table-simple', [
                            'procesosSimple' => $procesosSimple,
                        ])
                    </div>
                </div>

                {{-- MODAL REABRIR PROCESO --}}
                <div id="reabrirOverlay" class="modal-overlay hidden"></div>

                <div id="reabrirModal" class="modal-alert hidden">
                    <div class="modal-icon">⚠️</div>

                    <h3>Reabrir proceso</h3>

                    <p>
                        ¿Está seguro de reabrir este proceso?<br>
                        <small>El abogado podrá editarlo nuevamente.</small>
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

            </div>

        </div>
    </div>
    </div>

</x-app-layout>
