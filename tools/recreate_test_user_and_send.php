<?php
// Script: elimina usuario de prueba si existe, lo recrea y manda el mailable
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

$email = 'katzuno123@gmail.com';

// Eliminar si existe
$existing = Usuario::where('correo', $email)->first();
if ($existing) {
    echo "Eliminando usuario existente id={$existing->id_usuario} correo={$existing->correo}\n";
    $existing->delete();
}

// Crear nuevo usuario
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

echo "Usuario creado id={$user->id_usuario} correo={$user->correo}\n";
echo "Contraseña temporal: {$passwordPlain}\n";

// Enviar mailable (MAIL_MAILER=log por defecto en .env)
try {
    Mail::to($user->correo)->send(new App\Mail\UsuarioCreado($user, $passwordPlain));
    echo "Mailable disparado OK. Revisa storage/logs/laravel.log para ver el contenido.\n";
} catch (Exception $e) {
    echo "Error al enviar mailable: " . $e->getMessage() . "\n";
}

// Mostrar cola del log (últimas 200 líneas)
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (is_readable($logPath)) {
    echo "\n--- Últimas líneas de laravel.log ---\n";
    $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $tail = array_slice($lines, -200);
    foreach ($tail as $ln) echo $ln . "\n";
} else {
    echo "No se encuentra el log en: {$logPath}\n";
}

