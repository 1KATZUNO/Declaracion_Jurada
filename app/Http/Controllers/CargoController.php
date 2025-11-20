<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;

class CargoController extends Controller
{
    public function index(Request $request)
    { 
        // Obtener usuario actual
        $userId = $request->session()->get('usuario_id');
        $usuarioActual = $userId ? \App\Models\Usuario::find($userId) : null;
        
        $query = Cargo::select('id_cargo', 'nombre', 'descripcion', 'id_usuario')
            ->orderBy('nombre');
        
        // Si es funcionario, solo mostrar sus cargos
        if ($usuarioActual && $usuarioActual->rol === 'funcionario') {
            $query->where('id_usuario', $usuarioActual->id_usuario);
        }
        
        $cargos = $query->paginate(15); 
        return view('cargos.index',compact('cargos')); 
    }
    public function create(){ return view('cargos.create'); }
    public function store(Request $r){
        $data = $r->validate([
            'nombre'=>'required|string|max:100',
            'descripcion'=>'nullable|string'
        ]);
        $data['id_usuario'] = $r->session()->get('usuario_id');
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

