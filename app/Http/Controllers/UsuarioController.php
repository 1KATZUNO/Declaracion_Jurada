<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index() {
        $usuarios = Usuario::latest()->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create() { return view('usuarios.create'); }

    public function store(Request $request) {
        $data = $request->validate([
            'nombre'=>'required|string|max:50',
            'apellido'=>'required|string|max:50',
            'correo'=>'required|email|unique:usuario,correo',
            'contrasena'=>'required|min:8',
            'telefono'=>'nullable|string|max:20',
            'rol'=>'required|in:funcionario,admin',
        ]);
        $data['contrasena'] = Hash::make($data['contrasena']);
        Usuario::create($data);
        return redirect()->route('usuarios.index')->with('ok','Usuario creado');
    }

    public function edit($id) {
        $usuario = Usuario::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id) {
        $usuario = Usuario::findOrFail($id);
        $data = $request->validate([
            'nombre'=>'required|string|max:50',
            'apellido'=>'required|string|max:50',
            'correo'=>"required|email|unique:usuario,correo,{$usuario->id_usuario},id_usuario",
            'telefono'=>'nullable|string|max:20',
            'rol'=>'required|in:funcionario,admin',
        ]);
        if ($request->filled('contrasena')) $data['contrasena'] = Hash::make($request->contrasena);
        $usuario->update($data);
        return redirect()->route('usuarios.index')->with('ok','Usuario actualizado');
    }

    public function destroy($id) {
        Usuario::findOrFail($id)->delete();
        return back()->with('ok','Usuario eliminado');
    }
}
