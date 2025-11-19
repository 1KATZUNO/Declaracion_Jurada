<?php
// Script de ayuda: crea un usuario de prueba y envía el mailable (usa MAIL_MAILER=log)
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

$email = 'katzuno123@gmail.com';

// Evitar duplicados
$existing = Usuario::where('correo', $email)->first();
if ($existing) {
    echo "Usuario ya existe: id={$existing->id_usuario} correo={$existing->correo}\n";
    exit(0);
}

$passwordPlain = Str::random(10);

$user = Usuario::create([
    'nombre' => 'Katz',
    'apellido' => 'Uno',
    'identificacion' => '000000000',
    'correo' => $email,
    'telefono' => '',
    'rol' => 'funcionario',
    'contrasena' => Hash::make($passwordPlain),
]);

// Enviar mailable (con MAIL_MAILER=log esto quedará en storage/logs/laravel.log)
try {
    Mail::to($user->correo)->send(new App\Mail\UsuarioCreado($user, $passwordPlain));
    echo "Envio disparado (Mail::send).\n";
} catch (Exception $e) {
    echo "Error al enviar mailable: " . $e->getMessage() . "\n";
}

echo "Usuario creado id={$user->id_usuario} correo={$user->correo}\n";
echo "Contraseña temporal: {$passwordPlain}\n";

// Mostrar últimas líneas del log para verificar contenido del mail (si existe)
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (is_readable($logPath)) {
    echo "\n--- Últimas líneas de laravel.log ---\n";
    $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $tail = array_slice($lines, -120);
    foreach ($tail as $ln) echo $ln . "\n";
} else {
    echo "No se encontró el log en: {$logPath}\n";
}

