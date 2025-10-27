<?php 

namespace App\Http\Controllers; 

use App\Models\Usuario; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Mail;

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

        // Guardar informacion en sesion 
        session([ 
            'usuario_id' => $usuario->id_usuario ?? $usuario->id, 
            'usuario_nombre' => $usuario->nombre . ' ' . $usuario->apellido, 
            'usuario_rol' => $usuario->rol, 
        ]); 

        return redirect()->route('declaraciones.index'); 
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
        $request->session()->flush(); 
        return redirect()->route('login'); 
    } 
}