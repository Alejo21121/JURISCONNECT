<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciales de acceso</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 640px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
            padding: 40px;
        }

        .header {
            text-align: center;
        }

        .logo {
            width: 120px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            margin-top: 10px;
            color: #00471b;
        }

        .content {
            font-size: 16px;
            line-height: 1.8;
            margin-top: 30px;
        }

        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .button {
            background-color: #007a33;
            color: white;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .button:hover {
            background-color: #006124;
        }

        .footer {
            font-size: 13px;
            color: #6b7280;
            margin-top: 40px;
            text-align: center;
        }

        .credentials {
            margin-top: 20px;
        }

        .credentials ul {
            padding-left: 20px;
        }

        .credentials li {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://senasofiaplus.xyz/wp-content/uploads/2023/10/logo-del-sena-01.png" alt="Logo SENA"
                class="logo">
            <h1 class="title">Bienvenido(a) a JustConnect SENA</h1>
        </div>

        <div class="content">
            <p>Hola {{ $user->name }},</p>

            <p>Se ha creado tu cuenta en el sistema <strong>JustConnect SENA</strong>.</p>

            <div class="credentials">
                <p><strong>Tus credenciales de acceso son:</strong></p>
                <ul>
                    <li><strong>Correo:</strong> {{ $user->email }}</li>
                    <li><strong>Contraseña temporal:</strong> {{ $plainPassword }}</li>
                </ul>
            </div>

            <p>Por seguridad, debes cambiar tu contraseña. Haz clic en el siguiente botón:</p>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button"
                    style="color: #ffffff !important; text-decoration: none !important;">
                    Cambiar mi contraseña
                </a>
            </div>

            <p>Este enlace es válido durante los próximos <strong>15 minutos</strong>.</p>

            <div class="footer">
                <p>Gracias,<br>El equipo de JustConnect SENA</p>
                <p><small>Este es un mensaje automático. Por favor, no respondas a este correo.</small></p>
            </div>
        </div>
    </div>
</body>

</html>
