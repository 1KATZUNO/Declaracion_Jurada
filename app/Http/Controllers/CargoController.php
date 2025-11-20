<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index()
    { 
        $cargos = Cargo::select('id_cargo', 'nombre', 'descripcion')
            ->orderBy('nombre')
            ->paginate(15); 
        return view('cargos.index',compact('cargos')); 
    }
    public function create(){ return view('cargos.create'); }
    public function store(Request $r){
        $data = $r->validate([
            'nombre'=>'required|string|max:100',
            'descripcion'=>'nullable|string'
        ]);
        Cargo::create($data); return redirect()->route('cargos.index')->with('ok','Cargo creado');
    }
    public function edit($id){ $cargo = Cargo::findOrFail($id); return view('cargos.edit',compact('cargo')); }
    public function update(Request $r,$id){
        $cargo = Cargo::findOrFail($id);
        $data = $r->validate([
            'nombre'=>'required|string|max:100',
            'descripcion'=>'nullable|string'
        ]);
        $cargo->update($data); return redirect()->route('cargos.index')->with('ok','Cargo actualizado');
    }
    public function destroy($id){ Cargo::findOrFail($id)->delete(); return back()->with('ok','Cargo eliminado'); }
}

