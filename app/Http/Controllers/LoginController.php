<?php 

namespace App\Http\Controllers; 

use App\Models\Usuario; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash; 

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

    // Cerrar sesión 
    public function logout(Request $request) 
    { 
        $request->session()->flush(); 
        return redirect()->route('login'); 
    } 
}