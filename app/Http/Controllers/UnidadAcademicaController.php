<?php

namespace App\Http\Controllers;

use App\Models\UnidadAcademica;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnidadAcademicaController extends Controller
{
    public function index(Request $r) {
        $q = UnidadAcademica::with('sede')
            ->when($r->filled('search'), fn($qq) =>
                $qq->where('nombre','like','%'.$r->search.'%'))
            ->when($r->filled('sede_id'), fn($qq) =>
                $qq->where('id_sede', $r->sede_id))
            ->when($r->filled('estado'), fn($qq) =>
                $qq->where('estado', $r->estado))
            ->orderBy('nombre');

        $unidades = $q->paginate(10)->withQueryString();

        // Sedes SIN estado (falta que mi compañero que le toco Sede lo implemente)
        $sedes   = Sede::orderBy('nombre')->get(['id_sede','nombre']);
        $estados = ['ACTIVA','INACTIVA'];

        return view('unidades.index', compact('unidades','sedes','estados'));
    }

    public function create() {
        $sedes   = Sede::orderBy('nombre')->get();
        $estados = ['ACTIVA','INACTIVA'];
        return view('unidades.create', compact('sedes','estados'));
    }

    public function store(Request $r) {
        $data = $r->validate([
            'nombre'  => ['required','string','max:100'],
            'id_sede' => ['required','exists:sede,id_sede'],
            'estado'  => ['required', Rule::in(['ACTIVA','INACTIVA'])],
        ]);

        UnidadAcademica::create($data);
        return redirect()->route('unidades.index')->with('ok','Unidad creada');
    }

    public function edit($id) {
        $unidad  = UnidadAcademica::findOrFail($id);
        $sedes   = Sede::orderBy('nombre')->get();
        $estados = ['ACTIVA','INACTIVA'];
        return view('unidades.edit', compact('unidad','sedes','estados'));
    }

    public function update(Request $r, $id) {
        $unidad = UnidadAcademica::findOrFail($id);

        $data = $r->validate([
            'nombre'  => ['required','string','max:100'],
            'id_sede' => ['required','exists:sede,id_sede'],
            'estado'  => ['required', Rule::in(['ACTIVA','INACTIVA'])],
        ]);

        $unidad->update($data);
        return redirect()->route('unidades.index')->with('ok','Unidad actualizada');
    }

    public function destroy($id) {
        $unidad = UnidadAcademica::withCount('declaraciones')->findOrFail($id);

        // Si tiene declaraciones, NO borrar; inactivar para preservar histórico
        if ($unidad->declaraciones_count > 0) {
            $unidad->update(['estado' => 'INACTIVA']);
            return back()->with('ok','Unidad inactivada (tenía declaraciones asociadas).');
        }

        $unidad->delete(); // soft delete
        return back()->with('ok','Unidad eliminada');
    }

    public function catalogo(Request $r) {
        $soloActivas = filter_var($r->query('solo_activas','true'), FILTER_VALIDATE_BOOLEAN);
        $q = UnidadAcademica::query()->select('id_unidad as id','nombre','id_sede');
        if ($soloActivas) $q->where('estado','ACTIVA');
        return response()->json($q->orderBy('nombre')->get());
    }
}
