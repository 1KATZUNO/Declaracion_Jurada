<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(){
        $notificaciones = Notificacion::with('usuario')->latest('fecha_envio')->get();
        return view('notificaciones.index', compact('notificaciones'));
    }

    public function create(){
        return view('notificaciones.create', ['usuarios'=>Usuario::all()]);
    }

    public function store(Request $r){
        $data = $r->validate([
            'id_usuario'=>'required|exists:usuario,id_usuario',
            'mensaje'=>'required|string',
            'estado'=>'required|in:pendiente,enviada,leída',
        ]);
        Notificacion::create($data + ['fecha_envio'=>now()]);
        return redirect()->route('notificaciones.index')->with('ok','Notificación creada');
    }

    public function destroy($id){
        Notificacion::findOrFail($id)->delete();
        return back()->with('ok','Notificación eliminada');
    }
}
