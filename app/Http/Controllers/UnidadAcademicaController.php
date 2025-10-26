<?php

namespace App\Http\Controllers;

use App\Models\UnidadAcademica;
use App\Models\Sede;
use Illuminate\Http\Request;

class UnidadAcademicaController extends Controller
{
    public function index(Request $r) {
        $q = UnidadAcademica::with('sede')
            ->when($r->filled('search'), fn($qq) =>
                $qq->where('nombre', 'like', '%'.$r->search.'%'))
            ->when($r->filled('sede_id'), fn($qq) =>
                $qq->where('id_sede', $r->sede_id))
            ->when($r->filled('estado'), fn($qq) =>
                $qq->where('estado', $r->estado))
            ->orderBy('nombre');

        $unidades = $q->paginate(10)->withQueryString();

        $sedes   = Sede::orderBy('nombre')->get(['id_sede','nombre']);
        $estados = ['ACTIVA','INACTIVA'];

        return view('unidades.index', compact('unidades','sedes','estados'));
    }

    public function create() {
        $sedes = Sede::orderBy('nombre')->get(['id_sede','nombre']);
        return view('unidades.create', compact('sedes'));
    }

    public function store(Request $r) {
        $data = $r->validate([
            'nombre'  => ['required','string','max:100'],
            'id_sede' => ['required','exists:sede,id_sede'],
            // NO validar 'estado' porque no se envía desde el form, esto solo si metemos estado
        ]);

        if (!array_key_exists('estado', $data)) {
            $data['estado'] = 'ACTIVA';
        }

        UnidadAcademica::create($data);
        return redirect()->route('unidades.index')->with('ok','Unidad creada');
    }

    public function edit($id) {
        $unidad = UnidadAcademica::findOrFail($id);
        $sedes  = Sede::orderBy('nombre')->get(['id_sede','nombre']);
        return view('unidades.edit', compact('unidad','sedes'));
    }

    public function update(Request $r, $id) {
        $unidad = UnidadAcademica::findOrFail($id);

        $data = $r->validate([
            'nombre'  => ['required','string','max:100'],
            'id_sede' => ['required','exists:sede,id_sede'],
            // NO validar 'estado' aquí tampoco
        ]);

        $unidad->update($data);
        return redirect()->route('unidades.index')->with('ok','Unidad actualizada');
    }

    public function destroy($id) {
        $unidad = UnidadAcademica::withCount('declaraciones')->findOrFail($id);

        // Si tiene declaraciones, NO borrar; inactivar para guardar historial
        if ($unidad->declaraciones_count > 0) {
            $unidad->update(['estado' => 'INACTIVA']);
            return back()->with('ok','Unidad inactivada (tenía declaraciones asociadas).');
        }

        $unidad->delete(); // soft delete
        return back()->with('ok','Unidad eliminada');
    }

    // Catálogo JSON (para selects dependientes)
    public function catalogo(Request $r) {
        $soloActivas = filter_var($r->query('solo_activas','true'), FILTER_VALIDATE_BOOLEAN);
        $q = UnidadAcademica::query()->select('id_unidad as id','nombre','id_sede');
        if ($soloActivas && \Schema::hasColumn('unidad_academica','estado')) {
            $q->where('estado','ACTIVA');
        }
        return response()->json($q->orderBy('nombre')->get());
    }
}
