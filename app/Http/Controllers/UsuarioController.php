<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\UsuarioCreado;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

    // Actualizar perfil del usuario autenticado (nombre/apellido + avatar opcional)
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'nullable|string|max:50',
            'apellido' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048', // 2MB
        ]);

        $user = null;
        if (function_exists('auth') && auth()->check()) {
            $user = auth()->user();
        } elseif (session()->has('usuario_id')) {
            $user = Usuario::find(session('usuario_id'));
        }

        $avatarUrl = null;
        if ($request->hasFile('avatar')) {
            // Guardar en storage/app/public/avatars y obtener URL pública (/storage/avatars/...)
            $path = $request->file('avatar')->store('public/avatars');
            $avatarUrl = Storage::url($path); // => /storage/avatars/filename.jpg
        }

        if ($user) {
            // actualizar nombre/apellido en DB si vienen
            $update = [];
            if (isset($data['nombre'])) $update['nombre'] = $data['nombre'];
            if (isset($data['apellido'])) $update['apellido'] = $data['apellido'];

            // si la tabla usuario tiene columna 'avatar', guardarla; si no, la llevamos a sesión
            if ($avatarUrl && Schema::hasColumn($user->getTable(), 'avatar')) {
                // Guardar el path en BD para consistencia (usamos $path que es 'public/avatars/filename')
                $update['avatar'] = $path;
            }

            if (!empty($update)) {
                $user->update($update);
            }

            // actualizar sesión (nombre y avatar)
            session(['usuario_nombre' => trim(($update['nombre'] ?? $user->nombre) . ' ' . ($update['apellido'] ?? $user->apellido))]);
            if ($avatarUrl) {
                // preferir columna DB si existe
                if (Schema::hasColumn($user->getTable(), 'avatar')) {
                    // Si BD tuvo avatar guardado, obtener su URL pública desde Storage
                    $dbAvatar = $user->getAttribute('avatar') ?? null;
                    session(['usuario_avatar' => $dbAvatar ? Storage::url($dbAvatar) : $avatarUrl]);
                } else {
                    session(['usuario_avatar' => $avatarUrl]);
                }
            }
        } else {
            // si no hay usuario en DB (caso raro), solo actualizar sesión
            if (isset($data['nombre']) || isset($data['apellido'])) {
                $nombre = trim(($data['nombre'] ?? session('usuario.nombre') ?? '') . ' ' . ($data['apellido'] ?? session('usuario.apellido') ?? ''));
                session(['usuario_nombre' => $nombre]);
            }
            if ($avatarUrl) session(['usuario_avatar' => $avatarUrl]);
        }

        return back()->with('ok','Perfil actualizado');
    }
}
