<?php

namespace App\Http\Controllers;

use App\Models\UnidadAcademica;
use App\Models\Sede;
use Illuminate\Http\Request;

class UnidadAcademicaController extends Controller
{
    public function index() {
        $unidades = UnidadAcademica::with('sede')->get();
        return view('unidades.index', compact('unidades'));
    }
    public function create() { $sedes = Sede::all(); return view('unidades.create', compact('sedes')); }
    public function store(Request $r) {
        $data = $r->validate([
            'nombre'=>'required|string|max:100',
            'id_sede'=>'required|exists:sede,id_sede'
        ]);
        UnidadAcademica::create($data);
        return redirect()->route('unidades.index')->with('ok','Unidad creada');
    }
    public function edit($id) {
        $unidad = UnidadAcademica::findOrFail($id);
        $sedes = Sede::all();
        return view('unidades.edit',compact('unidad','sedes'));
    }
    public function update(Request $r,$id) {
        $unidad = UnidadAcademica::findOrFail($id);
        $data = $r->validate([
            'nombre'=>'required|string|max:100',
            'id_sede'=>'required|exists:sede,id_sede'
        ]);
        $unidad->update($data);
        return redirect()->route('unidades.index')->with('ok','Unidad actualizada');
    }
    public function destroy($id) { UnidadAcademica::findOrFail($id)->delete(); return back()->with('ok','Unidad eliminada'); }
}

