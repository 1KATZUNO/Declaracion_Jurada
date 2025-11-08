<!DOCTYPE html>
 @csrf
<html>
<head>
    <meta charset="utf-8">
    <title>Cuenta creada</title>
</head>
<body>
    <h2>Hola {{ $usuario->nombre }} {{ $usuario->apellido }}</h2>
    <p>Tu cuenta ha sido creada exitosamente.</p>

    <p><strong>Correo:</strong> {{ $usuario->correo }}</p>
    <p><strong>Contraseña temporal:</strong> {{ $passwordPlain }}</p>

    <p>Por motivos de seguridad, cambia tu contraseña al iniciar sesión.</p>

    <p>Saludos,<br>El equipo del sistema</p>
</body>
</html>
