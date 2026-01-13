<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Pagos de Sentencias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', system-ui, sans-serif;
        }

        body {
            background: radial-gradient(circle at top, #f0fdf4, #e5e7eb);
            margin: 0;
            padding: 24px;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .08);
            padding: 24px;
            margin-bottom: 20px;
            transition: all .35s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, .12);
        }

        .header h1 {
            font-size: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .stat {
            padding: 24px;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }

        .stat::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .35));
            transform: translateX(-100%);
            transition: .5s;
        }

        .stat:hover::after {
            transform: translateX(100%);
        }

        .stat.yellow {
            background: #fefce8;
            border-left: 6px solid #facc15;
        }

        .stat.green {
            background: #ecfdf5;
            border-left: 6px solid #22c55e;
        }

        .stat.blue {
            background: #eff6ff;
            border-left: 6px solid #3b82f6;
        }

        .filters button {
            padding: 12px 20px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all .3s ease;
            background: #f1f5f9;
        }

        .filters button:hover {
            transform: translateY(-2px);
        }

        .filters .active {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            box-shadow: 0 10px 25px rgba(22, 163, 74, .35);
        }

        .process {
            animation: slideUp .4s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge {
            padding: 8px 16px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge.green {
            background: #dcfce7;
            color: #166534;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(22, 163, 74, .4);
            }

            70% {
                box-shadow: 0 0 0 12px rgba(22, 163, 74, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(22, 163, 74, 0);
            }
        }

        .badge.yellow {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge.gray {
            background: #e5e7eb;
            color: #374151;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: all .25s ease;
        }

        .btn-green {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
        }

        .btn-green:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 12px 30px rgba(22, 163, 74, .45);
        }

        .btn-green:active {
            transform: scale(.96);
        }

        /* MODAL */
        .modal-bg {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99;
            animation: fadeIn .3s ease;
        }

        .modal {
            background: white;
            border-radius: 24px;
            max-width: 600px;
            width: 100%;
            animation: modalIn .4s ease;
        }

        @keyframes modalIn {
            from {
                transform: translateY(30px) scale(.95);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            padding: 24px;
            display: flex;
            justify-content: space-between;
        }

        .modal-body {
            padding: 24px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            transition: .2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, .25);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
    </style>

</head>

<body>

    <div class="container">

        <!-- HEADER -->
        <div class="card header">
            <h1><i class="fa-solid fa-dollar-sign"></i> Gestión de Pagos</h1>
            <strong>Total procesos: <span id="totalProcesos"></span></strong>
        </div>

        <!-- STATS -->
        <div class="stats">
            <div class="card stat yellow">
                <small>Pagos pendientes</small>
                <h2 id="pendientes"></h2>
            </div>
            <div class="card stat green">
                <small>Pagos realizados</small>
                <h2 id="pagados"></h2>
            </div>
            <div class="card stat blue">
                <small>Total pagado</small>
                <h2 id="totalPagado"></h2>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="card filters">
            <button onclick="setFiltro('todos')" class="active" id="fTodos">Todos</button>
            <button onclick="setFiltro('pendientes')" id="fPendientes">Pendientes</button>
            <button onclick="setFiltro('pagados')" id="fPagados">Pagados</button>
        </div>

        <!-- LISTA -->
        <div id="listaProcesos"></div>

    </div>

    <!-- MODAL -->
    <div class="modal-bg" id="modal">
        <div class="modal">
            <div class="modal-header">
                <h3>Registrar Pago</h3>
                <button onclick="cerrarModal()">✖</button>
            </div>
            <div class="modal-body">
                <p id="infoProceso"></p>

                <div class="field">
                    <label>Valor sentencia *</label>
                    <input type="number" id="valor">
                </div>

                <div class="field">
                    <label>Forma de pago</label>
                    <select id="forma">
                        <option>Cadena presupuestal</option>
                        <option>Fondo de contingencia</option>
                        <option>Título judicial</option>
                    </select>
                </div>

                <div class="field">
                    <label>Fecha de pago *</label>
                    <input type="date" id="fecha">
                </div>

                <div class="field">
                    <label>Observaciones</label>
                    <textarea id="obs"></textarea>
                </div>

                <div class="actions">
                    <button class="btn btn-green" onclick="guardarPago()">Registrar</button>
                    <button class="btn" onclick="cerrarModal()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let filtro = 'todos';
        let procesoActual = null;

        let procesos = [{
                id: 1,
                nombre: 'Proceso Laboral',
                radicado: '2024-001',
                requiere: true,
                pagado: false,
                estimado: 15000000
            },
            {
                id: 2,
                nombre: 'Proceso Contractual',
                radicado: '2024-002',
                requiere: true,
                pagado: true,
                valor: 8500000
            },
            {
                id: 3,
                nombre: 'Proceso Civil',
                radicado: '2024-003',
                requiere: false,
                pagado: false
            }
        ];

        function render() {
            let lista = document.getElementById('listaProcesos');
            lista.innerHTML = '';

            let filtrados = procesos.filter(p => {
                if (filtro === 'pendientes') return p.requiere && !p.pagado;
                if (filtro === 'pagados') return p.pagado;
                return true;
            });

            filtrados.forEach(p => {
                lista.innerHTML += `
    <div class="card process">
      <h3>${p.nombre}</h3>
      <p><b>Radicado:</b> ${p.radicado}</p>
      ${
        !p.requiere ? `<span class="badge gray">No requiere pago</span>` :
        p.pagado ? `<span class="badge green">Pago realizado</span>` :
        `<span class="badge yellow">Pago pendiente</span>
                 <button class="btn btn-green" onclick="abrirModal(${p.id})">Registrar pago</button>`
      }
    </div>`;
            });

            document.getElementById('totalProcesos').innerText = procesos.length;
            document.getElementById('pendientes').innerText = procesos.filter(p => p.requiere && !p.pagado).length;
            document.getElementById('pagados').innerText = procesos.filter(p => p.pagado).length;
            document.getElementById('totalPagado').innerText =
                procesos.filter(p => p.pagado).reduce((s, p) => s + (p.valor || 0), 0).toLocaleString('es-CO', {
                    style: 'currency',
                    currency: 'COP'
                });
        }

        function setFiltro(f) {
            filtro = f;
            document.querySelectorAll('.filters button').forEach(b => b.classList.remove('active'));
            document.getElementById('f' + f.charAt(0).toUpperCase() + f.slice(1)).classList.add('active');
            render();
        }

        function abrirModal(id) {
            procesoActual = procesos.find(p => p.id === id);
            document.getElementById('modal').style.display = 'flex';
            document.getElementById('infoProceso').innerText = procesoActual.nombre;
        }

        function cerrarModal() {
            document.getElementById('modal').style.display = 'none';
        }

        function guardarPago() {
            procesoActual.pagado = true;
            procesoActual.valor = parseInt(document.getElementById('valor').value);
            cerrarModal();
            render();
        }

        render();
    </script>

</body>

</html>
