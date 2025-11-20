<?php 

namespace App\Http\Controllers; 

use App\Models\Usuario; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class LoginController extends Controller 
{ 
    // Mostrar formulario de login 
    public function showLoginForm() 
    { 
        return view('auth.login'); 
    } 

    // Procesar login 
    public function login(Request $request) 
    { 
        $data = $request->validate([ 
            'username' => 'required|string', 
            'password' => 'required|string', 
        ]); 

        // Buscar usuario por correo o identificacion 
        $usuario = Usuario::where('correo', $data['username']) 
            ->orWhere('identificacion', $data['username']) 
            ->first(); 

        if (!$usuario) { 
            return back()->withErrors(['username' => 'Credenciales incorrectas'])->withInput(); 
        } 

        if (!Hash::check($data['password'], $usuario->contrasena)) { 
            return back()->withErrors(['password' => 'Credenciales incorrectas'])->withInput(); 
        } 

        // Normalizar avatar y guardar en sesión
        $avatarUrl = null;
        if (!empty($usuario->avatar)) {
            $avatarUrl = $this->normalizeAvatarPublicUrl($usuario->avatar);
        }

        // Generar token único para esta sesión
        $sessionToken = bin2hex(random_bytes(32));
        
        // Guardar datos de la sesión con el token como clave
        $sessionData = [
            'usuario_id' => $usuario->id_usuario ?? $usuario->id,
            'usuario_nombre' => trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? '')),
            'usuario_rol' => $usuario->rol,
            'usuario_avatar' => $avatarUrl,
            'created_at' => now(),
        ];
        
        // Almacenar en sesión usando el token como namespace
        session()->put('auth_sessions.' . $sessionToken, $sessionData);
        
        // También mantener compatibilidad con sesión tradicional
        session([
            'usuario_id' => $usuario->id_usuario ?? $usuario->id,
            'usuario_nombre' => trim(($usuario->nombre ?? '') . ' ' . ($usuario->apellido ?? '')),
            'usuario_rol' => $usuario->rol,
            'usuario_avatar' => $avatarUrl,
            'current_session_token' => $sessionToken,
        ]);

        return redirect()->route('declaraciones.index')->with('session_token', $sessionToken); 
    } 
    // Copia del helper para este controlador
    private function normalizeAvatarPublicUrl(?string $value): ?string
    {
        if (empty($value)) return null;
        if (preg_match('/^(?:https?:)?\\/\\//', $value)) return $value;
        if (strpos($value, '/storage/') === 0) return $value;
        if (strpos($value, 'storage/') === 0) return '/'.$value;
        if (strpos($value, 'public/') === 0) {
            return Storage::url($value);
        }
        try { return Storage::url($value); } catch (\Exception $e) { return asset($value); }
    }

    // Mostrar formulario de cambio de contraseña
public function showChangePasswordForm()
{
    return view('auth.change_password');
}

// Procesar cambio de contraseña
public function changePassword(Request $request)
{
    $data = $request->validate([
        'correo' => 'required|email',
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:6|confirmed',
    ]);

    $usuario = Usuario::where('correo', $data['correo'])->first();

    if (!$usuario || !Hash::check($data['current_password'], $usuario->contrasena)) {
        return back()->withErrors(['current_password' => 'Correo o contraseña actual incorrecta']);
    }

    // Actualizar contraseña
    $usuario->contrasena = Hash::make($data['new_password']);
    $usuario->save();

    // Enviar correo de confirmación
    Mail::raw("Hola {$usuario->nombre}, tu contraseña se ha cambiado correctamente.", function($message) use ($usuario) {
        $message->to($usuario->correo)
                ->subject('Cambio de contraseña exitoso');
    });

    return redirect()->route('login')->with('ok', 'Contraseña cambiada correctamente. Se ha enviado un correo de confirmación.');
}

    // Cerrar sesión 
    public function logout(Request $request) 
    {
        // Obtener el token de sesión actual
        $sessionToken = $request->cookie('app_session_token') 
                     ?? $request->input('_session_token')
                     ?? session('current_session_token');
        
        // Si hay un token específico, solo eliminar esa sesión
        if ($sessionToken) {
            $request->session()->forget('auth_sessions.' . $sessionToken);
        }
        
        // Limpiar la sesión tradicional (solo para esta ventana)
        $request->session()->forget([
            'usuario_id',
            'usuario_nombre',
            'usuario_rol',
            'usuario_avatar',
            'current_session_token',
        ]);
        
        // No hacer flush completo para no afectar otras ventanas
        // $request->session()->flush(); 
        
        return redirect()->route('login')->withCookie(cookie()->forget('app_session_token'));
    } 
}