<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\ComentarioRespuesta;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ComentarioRespuestaController extends Controller
{
    private function usuarioActual(Request $request = null): ?Usuario
    {
        if (function_exists('auth') && auth()->check()) {
            return auth()->user();
        }

        $req = $request ?: request();
        if ($req->session()->has('usuario_id')) {
            return Usuario::find($req->session()->get('usuario_id'));
        }

        return null;
    }

    // ADMIN o AUTOR responden en el hilo
    public function store(Request $request, $idComentario)
    {
        $user = $this->usuarioActual($request);
        if (!$user) {
            abort(403);
        }

        // Buscar el comentario
        $comentario = Comentario::findOrFail($idComentario);

        // Sólo puede responder el ADMIN o el AUTOR del comentario
        $esAdmin = $user->rol === 'admin';
        $esAutor = $comentario->id_usuario === $user->id_usuario;

        if (!($esAdmin || $esAutor)) {
            abort(403);
        }

        // No permitir respuestas si el hilo está cerrado
        if ($comentario->estado !== 'abierto') {
            return back()->with('ok', 'El hilo está cerrado.');
        }

        $data = $request->validate([
            'mensaje' => ['required','string','max:10000'],
        ]);

        ComentarioRespuesta::create([
            'id_comentario' => $comentario->id_comentario,
            'id_usuario'    => $user->id_usuario,
            'mensaje'       => $data['mensaje'],
        ]);

        return back()->with('ok', 'Respuesta publicada.');
    }

    public function update(Request $request, $id)
    {
        $user = $this->usuarioActual($request);
        // Solo admin puede editar respuestas
        if (!$user || $user->rol !== 'admin') {
            abort(403);
        }

        $respuesta = ComentarioRespuesta::findOrFail($id);

        $data = $request->validate([
            'mensaje' => ['required','string','max:10000'],
        ]);

        $respuesta->update($data);

        return back()->with('ok', 'Respuesta actualizada.');
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->usuarioActual($request);
        // Solo admin puede eliminar respuestas
        if (!$user || $user->rol !== 'admin') {
            abort(403);
        }

        ComentarioRespuesta::findOrFail($id)->delete();

        return back()->with('ok', 'Respuesta eliminada.');
    }
}
