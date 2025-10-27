<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\UsuarioCreado;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::orderBy('id_usuario','desc')->paginate(15);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }
public function store(Request $request)
{
    $data = $request->validate([
        'nombre' => 'required|string|max:50',
        'apellido' => 'required|string|max:50',
        'identificacion' => 'required|string|max:20',
        'correo' => 'required|email|max:100|unique:usuario,correo',
        'telefono' => 'nullable|string|max:20',
        'rol' => 'required|in:funcionario,admin',
    ]);

    // Generar contraseña 
    $passwordPlain = Str::random(10);
    $data['contrasena'] = Hash::make($passwordPlain);

    // Crear usuario
    $usuario = Usuario::create($data);

    // Enviar correo
    Mail::to($usuario->correo)->send(new UsuarioCreado($usuario, $passwordPlain));

    return redirect()->route('usuarios.index')->with('ok', 'Usuario creado y contraseña enviada por correo.');
}

    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'identificacion' => 'required|string|max:20',
            'correo' => 'required|email|max:100|unique:usuario,correo,'.$usuario->id_usuario.',id_usuario',
            'telefono' => 'nullable|string|max:20',
            'contrasena' => 'nullable|string|min:6',
            'rol' => 'required|in:funcionario,admin',
        ]);

        if (!empty($data['contrasena'])) {
            $data['contrasena'] = bcrypt($data['contrasena']);
        } else {
            unset($data['contrasena']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('ok','Usuario actualizado');
    }

    public function destroy($id)
    {
        Usuario::findOrFail($id)->delete();
        return back()->with('ok','Usuario eliminado');
    }
}
