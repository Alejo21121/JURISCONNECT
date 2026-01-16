<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Asistentes</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        /* ===== MARCA DE AGUA ===== */
        .watermark {
            position: fixed;
            top: 45%;
            /* baja un poco */
            left: 45%;
            width: 400px;
            /* tamaño controlado */
            opacity: 0.07;
            /* bien clarita */
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px;
            text-align: left;
        }
    </style>
</head>

<body>

    <!-- MARCA DE AGUA -->
    <img src="{{ public_path('img/LogoInsti.png') }}" class="watermark">


    <h2>Listado de Asistentes</h2>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Tipo Doc</th>
                <th>N° Documento</th>
                <th>Correo</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assistants as $assistant)
                <tr>
                    <td>{{ $assistant->nombre }}</td>
                    <td>{{ $assistant->apellido }}</td>
                    <td>{{ $assistant->tipo_documento }}</td>
                    <td>{{ $assistant->numero_documento }}</td>
                    <td>{{ $assistant->correo }}</td>
                    <td>{{ $assistant->telefono }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
