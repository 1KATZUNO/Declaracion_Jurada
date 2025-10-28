<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //  usamos DB para leer la tabla 'declaracion'

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
        // Trae opciones para el select si no llega la FK por URL
        $declaraciones = DB::table('declaracion') 
            ->orderBy('id_declaracion', 'desc')
            ->get(['id_declaracion']);

        return view('horarios.create', compact('declaraciones'));
    }

    // Guardar nuevo horario
    public function store(Request $request)
    {
        // Evita caída si no viene la llave foranea
        if (!$request->filled('id_declaracion')) {
            return back()
                ->withErrors(['id_declaracion' => 'Falta la declaración a la que pertenece el horario.'])
                ->withInput();
        }

        $validated = $request->validate([
            //  usa la tabla REAL 'declaracion' (singular)
            'id_declaracion' => 'required|exists:declaracion,id_declaracion',
            'tipo'           => 'required|in:ucr,externo',
            'dia'            => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'hora_inicio'    => 'required|date_format:H:i',
            'hora_fin'       => 'required|date_format:H:i|after:hora_inicio',
            'lugar'          => 'nullable|string|max:255',
        ]);
//Crea horario correctamente
        Horario::create([
            'id_declaracion' => $validated['id_declaracion'],
            'tipo'           => $validated['tipo'],
            'dia'            => $validated['dia'],
            'hora_inicio'    => $validated['hora_inicio'],
            'hora_fin'       => $validated['hora_fin'],
            'lugar'          => $validated['lugar'] ?? null,
        ]);

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
