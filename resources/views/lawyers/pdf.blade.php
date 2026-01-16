<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Abogados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        /* Logo de fondo */

        .background-logo {
            position: fixed;
            top: 30%;
            left: 20%;
            opacity: 0.10;
            width: 400px;
            z-index: -1;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        /* Encabezado fijo */
        header {
            position: fixed;
            top: -10px;
            left: 0;
            right: 0;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }

        /* Pie de página */
        footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #2c3e50;
            padding-top: 5px;
            color: #555;
        }
    </style>
</head>

<body>

    <!-- Logo de fondo -->
    <img src="file://{{ $logoPath }}" class="background-logo">


    <!-- Encabezado -->
    <header>
        <h1>Listado de Abogados</h1>
    </header>

    <main style="margin-top: 60px;">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Tipo Doc</th>
                    <th>Documento</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Especialidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lawyers as $lawyer)
                    <tr>
                        <td>{{ $lawyer->nombre }}</td>
                        <td>{{ $lawyer->apellido }}</td>
                        <td>{{ $lawyer->tipo_documento }}</td>
                        <td>{{ $lawyer->numero_documento }}</td>
                        <td>{{ $lawyer->correo }}</td>
                        <td>{{ $lawyer->telefono }}</td>
                        <td>{{ $lawyer->especialidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

    <!-- Pie de página -->
    <footer>
        &copy; {{ date('Y') }} - Sistema de Gestión Jurídica | Generado el {{ date('d/m/Y H:i') }}
    </footer>

</body>

</html>
