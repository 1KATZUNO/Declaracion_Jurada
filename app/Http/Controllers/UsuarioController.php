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

        $path = null;
        if ($request->hasFile('avatar')) {
            // Guardar en storage/app/public/avatars
            $path = $request->file('avatar')->store('public/avatars'); // ej. "public/avatars/xxx.jpg"
        }

        if ($user) {
            $update = [];
            if (isset($data['nombre'])) $update['nombre'] = $data['nombre'];
            if (isset($data['apellido'])) $update['apellido'] = $data['apellido'];

            if ($path && Schema::hasColumn($user->getTable(), 'avatar')) {
                // Guardar path en BD (public/avatars/...)
                $update['avatar'] = $path;
            }

            if (!empty($update)) {
                $user->update($update);
            }

            // REFRESCAR modelo y actualizar el usuario autenticado
            $user = $user->fresh();
            if (function_exists('auth') && auth()->check()) {
                auth()->setUser($user);
            }

            // construir URL pública final del avatar (maneja distintos formatos guardados)
            $publicAvatar = null;
            if (Schema::hasColumn($user->getTable(), 'avatar') && !empty($user->avatar)) {
                $publicAvatar = $this->normalizeAvatarPublicUrl($user->avatar);
            } elseif ($path) {
                $publicAvatar = $this->normalizeAvatarPublicUrl($path);
            }

            // actualizar sesión (nombre y avatar pública)
            session(['usuario_nombre' => trim(($update['nombre'] ?? $user->nombre) . ' ' . ($update['apellido'] ?? $user->apellido))]);
            if ($publicAvatar) {
                session(['usuario_avatar' => $publicAvatar]);
            }
        } else {
            // caso sin usuario: solo sesión
            if (isset($data['nombre']) || isset($data['apellido'])) {
                $nombre = trim(($data['nombre'] ?? session('usuario.nombre') ?? '') . ' ' . ($data['apellido'] ?? session('usuario.apellido') ?? ''));
                session(['usuario_nombre' => $nombre]);
            }
            if ($path) session(['usuario_avatar' => $this->normalizeAvatarPublicUrl($path)]);
        }

        return back()->with('ok','Perfil actualizado');
    }

    // Helper privado: devuelve URL pública utilizable por la vista
    private function normalizeAvatarPublicUrl(?string $value): ?string
    {
        if (empty($value)) return null;

        // Si ya es url absoluta o protocolo relativo
        if (preg_match('/^(?:https?:)?\\/\\//', $value)) return $value;

        // Si ya empieza con /storage — es la URL pública esperada
        if (strpos($value, '/storage/') === 0) return $value;

        // Si empieza con 'storage/' (sin slash) -> añadir slash
        if (strpos($value, 'storage/') === 0) return '/'.$value;

        // Si empieza con 'public/' -> Storage::url lo normaliza a /storage/...
        if (strpos($value, 'public/') === 0) {
            return \Illuminate\Support\Facades\Storage::url($value);
        }

        // cualquier otro (por ejemplo guardado directamente como 'avatars/..' o 'public/..'), intentar Storage::url
        try {
            return \Illuminate\Support\Facades\Storage::url($value);
        } catch (\Exception $e) {
            // fallback: asset si parece una ruta relativa
            return asset($value);
        }
    }
}
