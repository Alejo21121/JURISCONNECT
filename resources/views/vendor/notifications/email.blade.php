<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña - JustConnect SENA</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8; margin: 0; padding: 0; }
        .container { max-width: 640px; margin: 30px auto; background-color: #fff; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.08); padding: 40px; }
        .header { text-align: center; }
        .logo { width: 120px; margin-bottom: 10px; }
        .title { font-size: 22px; font-weight: 600; color: #00471b; }
        .content { font-size: 16px; line-height: 1.6; margin-top: 30px; }
        .button-container { text-align: center; margin: 30px 0; }
        .button { background-color: #007a33; color: #fff; padding: 14px 32px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; }
        .button:hover { background-color: #006124; }
        .footer { font-size: 13px; color: #000000ff; margin-top: 40px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://senasofiaplus.xyz/wp-content/uploads/2023/10/logo-del-sena-01.png" alt="Logo SENA" class="logo">
            <h1 class="title"><center>Restablecer Contraseña</center></h1>
        </div>

        <div class="content">
            <p>Has solicitado restablecer tu contraseña en el sistema <strong>JustConnect SENA</strong>.</p>
            <p>Haz clic en el siguiente botón para continuar:</p>

            <div class="button-container">
                <a href="{{ $actionUrl }}" class="button">Cambiar mi contraseña</a>

            </div>

            <p>Este enlace es válido durante los próximos <strong>15 minutos</strong>.</p>
            <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        </div>  

        <div class="footer">
            <p>Gracias,<br>El equipo de JustConnect SENA</p>
            <p><small>Este es un mensaje automático. Por favor, no respondas a este correo.</small></p>
        </div>
    </div>
</body>
</html>
