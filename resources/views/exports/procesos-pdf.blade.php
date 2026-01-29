<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Procesos</title>
    <style>
        body {
            font-family: sans-serif;
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
    <h2>Listado de Procesos</h2>
    <table>
        <thead>
            <tr>
                <th>Radicado</th>
                <th>Tipo</th>
                <th>Demandante</th>
                <th>Demandado</th>
                <th>Estado</th>
                <th>Fecha radicación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($procesos as $proceso)
            <tr>
                <td>{{ $proceso->numero_radicado }}</td>
                <td>{{ $proceso->tipo_proceso }}</td>
                <td>{{ $proceso->demandante }}</td>
                <td>{{ $proceso->demandado }}</td>
                <td>{{ $proceso->estado }}</td>
                <td>{{ $proceso->created_at }}</td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>