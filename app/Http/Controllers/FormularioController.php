<?php

namespace App\Http\Controllers;

use App\Models\Formulario;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index()
    { 
        $formularios = Formulario::select('id_formulario', 'nombre', 'titulo', 'descripcion', 'fecha_creacion')
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(15); 
        return view('formularios.index',compact('formularios')); 
    }
    public function create(){ return view('formularios.create'); }
    public function store(Request $r){
        $data = $r->validate([
            'titulo'=>'required|string|max:200',
            'descripcion'=>'nullable|string',
            'fecha_creacion'=>'required|date'
        ]);
        Formulario::create($data); return redirect()->route('formularios.index')->with('ok','Formulario creado');
    }
    public function edit($id){ $formulario = Formulario::findOrFail($id); return view('formularios.edit',compact('formulario')); }
    public function update(Request $r,$id){
        $formulario = Formulario::findOrFail($id);
        $data = $r->validate([
            'titulo'=>'required|string|max:200',
            'descripcion'=>'nullable|string',
            'fecha_creacion'=>'required|date'
        ]);
        $formulario->update($data); return redirect()->route('formularios.index')->with('ok','Formulario actualizado');
    }
    public function destroy($id){ Formulario::findOrFail($id)->delete(); return back()->with('ok','Formulario eliminado'); }
}



