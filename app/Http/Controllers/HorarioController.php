<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    // Mostrar todos los horarios
    public function index()
    {
        $horarios = Horario::orderBy('id_horario', 'desc')->paginate(10);
        return view('horarios.index', compact('horarios'));
    }

    // Formulario de creación
    public function create()
    {
        return view('horarios.create');
    }

    // Guardar nuevo horario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:ucr,externo',
            'dia' => 'required|string',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
        ]);

        Horario::create($validated);

        return redirect()->route('horarios.index')
                         ->with('success', 'Horario registrado correctamente');
    }

    // Eliminar horario
    public function destroy($id)
    {
        $horario = Horario::findOrFail($id);
        $horario->delete();

        return redirect()->route('horarios.index')
                         ->with('success', 'Horario eliminado correctamente');
    }
}

