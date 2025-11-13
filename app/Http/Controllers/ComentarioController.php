<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    /**
     * Obtiene el usuario actual usando auth() o la sesión
     */
    private function usuarioActual(Request $request = null): ?Usuario
    {
        // Si algún día usas auth()->login(), esto funcionará
        if (function_exists('auth') && auth()->check()) {
            return auth()->user();
        }

        // Tu esquema actual: usuario guardado en la sesión
        $req = $request ?: request();
        if ($req->session()->has('usuario_id')) {
            return Usuario::find($req->session()->get('usuario_id'));
        }

        return null;
    }

    // Lista del funcionario (solo sus comentarios)
    public function index(Request $request)
    {
        $user = $this->usuarioActual($request);
        if (!$user) {
            abort(403);
        }

        // Si es ADMIN, lo mandamos al listado global
        if ($user->rol === 'admin') {
            return redirect()->route('admin.comentarios.index');
        }

        // Funcionario: solo sus comentarios
        $comentarios = Comentario::with('respuestas.autor')
            ->where('id_usuario', $user->id_usuario)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('comentarios.index', compact('comentarios'));
    }

    public function create(Request $request)
    {
        $user = $this->usuarioActual($request);
        if (!$user) abort(403);

        return view('comentarios.create');
    }

    public function store(Request $request)
    {
        $user = $this->usuarioActual($request);
        if (!$user) abort(403);

        $data = $request->validate([
            'titulo'  => ['nullable','string','max:200'],
            'mensaje' => ['required','string','max:10000'],
        ]);

        $data['id_usuario'] = $user->id_usuario;
        // por defecto estado = 'abierto' desde la migración
        $comentario = Comentario::create($data);

        return redirect()
            ->route('comentarios.show', $comentario->id_comentario)
            ->with('ok', 'Comentario enviado correctamente.');
    }

    public function show(Request $request, $id)
    {
        $user = $this->usuarioActual($request);
        if (!$user) abort(403);

        $comentario = Comentario::with(['autor','respuestas.autor'])->findOrFail($id);

        // Permitir ver al autor (funcionario) y a admins
        if ($user->rol !== 'admin' && $comentario->id_usuario !== $user->id_usuario) {
            abort(403);
        }

        return view('comentarios.show', compact('comentario'));
    }

    // Edita SOLO el autor y si está abierto
    public function edit(Request $request, $id)
    {
        $user = $this->usuarioActual($request);
        if (!$user) abort(403);

        $comentario = Comentario::findOrFail($id);
        if ($comentario->id_usuario !== $user->id_usuario || $comentario->estado !== 'abierto') {
            abort(403);
        }

        return view('comentarios.edit', compact('comentario'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->usuarioActual($request);
        if (!$user) abort(403);

        $comentario = Comentario::findOrFail($id);
        if ($comentario->id_usuario !== $user->id_usuario || $comentario->estado !== 'abierto') {
            abort(403);
        }

        $data = $request->validate([
            'titulo'  => ['nullable','string','max:200'],
            'mensaje' => ['required','string','max:10000'],
        ]);

        $comentario->update($data);

        return redirect()
            ->route('comentarios.show', $comentario->id_comentario)
            ->with('ok', 'Comentario actualizado.');
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->usuarioActual($request);
        if (!$user) abort(403);

        $comentario = Comentario::findOrFail($id);
        if ($comentario->id_usuario !== $user->id_usuario || $comentario->estado !== 'abierto') {
            abort(403);
        }

        $comentario->delete();

        return redirect()
            ->route('comentarios.index')
            ->with('ok','Comentario eliminado.');
    }

    // ADMIN: listado global
    public function adminIndex(Request $request)
    {
        $user = $this->usuarioActual($request);
        if (!$user || $user->rol !== 'admin') {
            abort(403);
        }

        $comentarios = Comentario::with(['autor','respuestas.autor'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.comentarios.index', compact('comentarios'));
    }

    // ADMIN: cerrar / reabrir
    public function cambiarEstado(Request $request, $id)
    {
        $user = $this->usuarioActual($request);
        if (!$user || $user->rol !== 'admin') {
            abort(403);
        }

        $comentario = Comentario::findOrFail($id);

        $data = $request->validate([
            'estado' => ['required','in:abierto,cerrado'],
        ]);

        $comentario->update(['estado' => $data['estado']]);

        return back()->with('ok', 'Estado actualizado.');
    }
}
