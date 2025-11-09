<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

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
