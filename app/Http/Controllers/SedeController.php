<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    public function index() { $sedes = Sede::all(); return view('sedes.index', compact('sedes')); }
    public function create() { return view('sedes.create'); }
    public function store(Request $r) {
        $data = $r->validate(['nombre'=>'required|string|max:100','ubicacion'=>'nullable|string|max:150']);
        Sede::create($data); return redirect()->route('sedes.index')->with('ok','Sede creada');
    }
    public function edit($id) { $sede = Sede::findOrFail($id); return view('sedes.edit',compact('sede')); }
    public function update(Request $r,$id) {
        $sede = Sede::findOrFail($id);
        $data = $r->validate(['nombre'=>'required|string|max:100','ubicacion'=>'nullable|string|max:150']);
        $sede->update($data); return redirect()->route('sedes.index')->with('ok','Sede actualizada');
    }
    public function destroy($id) { Sede::findOrFail($id)->delete(); return back()->with('ok','Sede eliminada'); }
}

