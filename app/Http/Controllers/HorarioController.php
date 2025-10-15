<?php

namespace App\Http\Controllers;

use App\Models\{Horario,Declaracion};
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(){
        $horarios = Horario::with('declaracion.usuario')->get();
        return view('horarios.index', compact('horarios'));
    }

    public function create(){
        return view('horarios.create', ['declaraciones'=>Declaracion::with('usuario')->get()]);
    }

    public function store(Request $r){
        $data = $r->validate([
            'id_declaracion'=>'required|exists:declaracion,id_declaracion',
            'dia'=>'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio'=>'required|date_format:H:i',
            'hora_fin'=>'required|date_format:H:i|after:hora_inicio',
        ]);
        Horario::create($data);
        return redirect()->route('horarios.index')->with('ok','Horario creado');
    }

    public function edit($id){
        return view('horarios.edit', [
            'horario'=>Horario::findOrFail($id),
            'declaraciones'=>Declaracion::with('usuario')->get(),
        ]);
    }

    public function update(Request $r,$id){
        $h = Horario::findOrFail($id);
        $data = $r->validate([
            'id_declaracion'=>'required|exists:declaracion,id_declaracion',
            'dia'=>'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio'=>'required|date_format:H:i',
            'hora_fin'=>'required|date_format:H:i|after:hora_inicio',
        ]);
        $h->update($data);
        return redirect()->route('horarios.index')->with('ok','Horario actualizado');
    }

    public function destroy($id){
        Horario::findOrFail($id)->delete();
        return back()->with('ok','Horario eliminado');
    }
}

