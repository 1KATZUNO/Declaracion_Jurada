<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    public function index(Request $r)
    {
        $query = Sede::query();

        if ($r->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $r->nombre . '%');
        }

        if ($r->filled('ubicacion')) {
            $query->where('ubicacion', 'like', '%' . $r->ubicacion . '%');
        }

        $sedes = $query->orderBy('nombre')->paginate(10)->withQueryString();

        return view('sedes.index', compact('sedes'));
    }

    public function create()
    {
        return view('sedes.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nombre' => 'required|string|max:100',
            'ubicacion' => 'nullable|string|max:150'
        ]);

        Sede::create($data);
        return redirect()->route('sedes.index')->with('ok', 'Sede creada correctamente');
    }

    public function edit($id)
    {
        $sede = Sede::findOrFail($id);
        return view('sedes.edit', compact('sede'));
    }

    public function update(Request $r, $id)
    {
        $sede = Sede::findOrFail($id);
        $data = $r->validate([
            'nombre' => 'required|string|max:100',
            'ubicacion' => 'nullable|string|max:150'
        ]);

        $sede->update($data);
        return redirect()->route('sedes.index')->with('ok', 'Sede actualizada correctamente');
    }

    public function destroy($id)
    {
        Sede::findOrFail($id)->delete();
        return back()->with('ok', 'Sede eliminada correctamente');
    }
}


