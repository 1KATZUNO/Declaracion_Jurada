<?php
// Script: regenera contraseña temporal para usuario existente y manda el mailable
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Usuario;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

$email = 'katzuno123@gmail.com';

$user = Usuario::where('correo', $email)->first();
if (! $user) {
    echo "Usuario no encontrado: {$email}\n";
    exit(1);
}

$passwordPlain = Str::random(10);
$user->contrasena = Hash::make($passwordPlain);
$user->save();

try {
    Mail::to($user->correo)->send(new App\Mail\UsuarioCreado($user, $passwordPlain));
    echo "Mailable enviado (log). Contraseña temporal: {$passwordPlain}\n";
} catch (Exception $e) {
    echo "Error al enviar: " . $e->getMessage() . "\n";
}

// Mostrar cola del log (últimas 200 líneas)
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (is_readable($logPath)) {
    echo "\n--- Últimas líneas de laravel.log ---\n";
    $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $tail = array_slice($lines, -200);
    foreach ($tail as $ln) echo $ln . "\n";
}

