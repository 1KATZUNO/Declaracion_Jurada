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

        // Usar nuestro modelo personalizado de notificaciones
        $notificaciones = \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
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

        // Usar nuestro modelo personalizado
        $notificacion = \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
            ->where('id_notificacion', $id)
            ->firstOrFail();

        // Marcar como leída
        if (!$notificacion->leida) {
            $notificacion->update([
                'leida' => true,
                'fecha_lectura' => now()
            ]);
        }

        return view('notificaciones.show', compact('notificacion'));
    }

    public function update(Request $request, $id)
    {
        $usuario = $this->usuarioActual($request);
        if (!$usuario) abort(403);

        // Usar nuestro modelo personalizado
        $notificacion = \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
            ->where('id_notificacion', $id)
            ->firstOrFail();

        $notificacion->update([
            'leida' => true,
            'fecha_lectura' => now()
        ]);

        return back()->with('ok', 'Notificación marcada como leída');
    }

    public function destroy(Request $request, $id)
    {
        $usuario = $this->usuarioActual($request);
        if (!$usuario) abort(403);

        // Usar nuestro modelo personalizado
        $notificacion = \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
            ->where('id_notificacion', $id)
            ->firstOrFail();

        $notificacion->delete();

        return back()->with('ok', 'Notificación eliminada');
    }

    public function marcarTodasLeidas(Request $request)
    {
        $usuario = $this->usuarioActual($request);
        if ($usuario) {
            // Marcar nuestras notificaciones personalizadas como leídas
            \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
                ->where('leida', false)
                ->update([
                    'leida' => true,
                    'fecha_lectura' => now()
                ]);

            // También marcar las notificaciones de Laravel por compatibilidad
            $usuario->unreadNotifications->markAsRead();
        }

        return back()->with('ok', 'Todas las notificaciones marcadas como leídas');
    }

    /**
     * Obtener notificaciones no leídas vía AJAX
     */
    public function getUnread(Request $request)
    {
        $usuario = $this->usuarioActual($request);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Contar TODAS las notificaciones no leídas
        $totalUnread = \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
            ->where('leida', false)
            ->count();

        // Obtener solo las últimas 5 para mostrar en el dropdown
        $unreadNotifications = \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $notificaciones = $unreadNotifications->map(function ($n) {
            // Icono según el tipo
            $icon = '';
            switch($n->tipo) {
                case 'crear': $icon = '✅'; break;
                case 'editar': $icon = '✏️'; break;
                case 'eliminar': $icon = '🗑️'; break;
                case 'exportar': $icon = '📄'; break;
                case 'vencimiento': $icon = '⚠️'; break;
                default: $icon = '🔔'; break;
            }

            // URL según el tipo de notificación
            if ($n->id_declaracion) {
                $url = route('declaraciones.show', $n->id_declaracion);
            } else {
                $url = route('notificaciones.index');
            }

            return [
                'id' => $n->id_notificacion,
                'titulo' => $n->titulo ?? 'Notificación del Sistema',
                'mensaje' => \Str::limit($n->mensaje ?? '', 80),
                'tipo' => $n->tipo,
                'icon' => $icon,
                'url' => $url,
                'created_at' => $n->created_at->diffForHumans(),
                'created_at_iso' => $n->created_at->toISOString(),
            ];
        });

        return response()->json([
            'count' => $totalUnread, // Usar el contador total, no solo las 5 mostradas
            'notifications' => $notificaciones,
        ]);
    }
}
