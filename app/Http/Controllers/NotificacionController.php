<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Notifications\NotificacionPersonalizada;

class NotificacionController extends Controller
{
    private function usuarioActual(Request $request)
    {
        if (function_exists('auth') && auth()->check()) {
            return auth()->user();
        }

        if ($request->session()->has('usuario_id')) {
            return Usuario::find($request->session()->get('usuario_id'));
        }

        return null;
    }

    public function index(Request $request)
    {
        $usuario = $this->usuarioActual($request);
        if (!$usuario) abort(403);

        $notificaciones = $usuario->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function create()
    {
        $usuarios = Usuario::orderBy('nombre')->orderBy('apellido')->get();

        return view('notificaciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_usuario' => ['required', 'exists:usuario,id_usuario'],
            'mensaje'    => ['required', 'string', 'max:5000'],
            'estado'     => ['required', 'in:pendiente,enviada,leída'],
        ]);

        $usuario = Usuario::findOrFail($data['id_usuario']);

        $usuario->notify(new NotificacionPersonalizada(
            $data['mensaje'],
            $data['estado']
        ));

        if ($data['estado'] === 'leída') {
            $usuario->notifications()->latest()->first()?->markAsRead();
        }

        return redirect()
            ->route('notificaciones.index')
            ->with('ok', 'Notificación enviada correctamente.');
    }

    public function show(Request $request, $id)
    {
        $usuario = $this->usuarioActual($request);
        if (!$usuario) abort(403);

        $notificacion = $usuario->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if (is_null($notificacion->read_at)) {
            $notificacion->markAsRead();
        }


        return view('notificaciones.show', compact('notificacion'));
    }

    public function destroy(Request $request, $id)
    {
        $usuario = $this->usuarioActual($request);
        if (!$usuario) abort(403);

        $notificacion = $usuario->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notificacion->delete();

        return back()->with('ok', 'Notificación eliminada');
    }

    public function marcarTodasLeidas(Request $request)
    {
        $usuario = $this->usuarioActual($request);
        if ($usuario) {
            $usuario->unreadNotifications->markAsRead();
        }

        return back()->with('ok', 'Todas las notificaciones marcadas como leídas');
    }
}
