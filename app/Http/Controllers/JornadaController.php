<?php

namespace App\Http\Controllers;

use App\Models\Jornada;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class JornadaController extends Controller
{
    public function index()
    {
        $jornadas = Jornada::orderBy('horas_por_semana', 'asc')->paginate(10);
        return view('jornadas.index', compact('jornadas'));
    }

    public function create()
    {
        $jornada = new Jornada();
        return view('jornadas.form', [
            'jornada' => $jornada,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|string|max:20|unique:jornada,tipo',
            'horas_por_semana' => 'required|integer|min:1|max:168',
        ], [
            'tipo.required' => 'El tipo es obligatorio (ej: 1/8, 1/4, 1/2, 3/4, TC).',
            'tipo.unique' => 'Ya existe una jornada con ese tipo.',
            'horas_por_semana.required' => 'Las horas por semana son obligatorias.',
            'horas_por_semana.integer' => 'Las horas deben ser un número entero.',
            'horas_por_semana.min' => 'Debe ser al menos 1 hora.',
            'horas_por_semana.max' => 'No puede exceder 168 horas.',
        ]);

        Jornada::create($data);

        return redirect()->route('jornadas.index')
            ->with('success', 'Jornada creada correctamente.');
    }

    public function edit($id)
    {
        $jornada = Jornada::findOrFail($id);
        return view('jornadas.form', [
            'jornada' => $jornada,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, $id)
    {
        $jornada = Jornada::findOrFail($id);

        $data = $request->validate([
            'tipo' => 'required|string|max:20|unique:jornada,tipo,' . $jornada->id_jornada . ',id_jornada',
            'horas_por_semana' => 'required|integer|min:1|max:168',
        ]);

        $jornada->update($data);

        return redirect()->route('jornadas.index')
            ->with('success', 'Jornada actualizada correctamente.');
    }

    public function destroy($id)
    {
        $jornada = Jornada::findOrFail($id);

        // Opcional: impide borrar si hay horarios que la usan
        $usos = Horario::where('id_jornada', $jornada->id_jornada)->count();
        if ($usos > 0) {
            return back()->withErrors(['jornada' => 'No se puede eliminar: hay horarios que usan esta jornada.']);
        }

        try {
            $jornada->delete();
            return redirect()->route('jornadas.index')->with('success', 'Jornada eliminada correctamente.');
        } catch (QueryException $e) {
            return back()->withErrors(['jornada' => 'No se pudo eliminar la jornada (restricciones de integridad).']);
        }
    }
}
