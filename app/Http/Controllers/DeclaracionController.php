<?php

namespace App\Http\Controllers;

use App\Models\Declaracion;
use App\Models\Usuario;
use App\Models\Formulario;
use App\Models\UnidadAcademica;
use App\Models\Cargo;
use Illuminate\Http\Request;

class DeclaracionController extends Controller
{
    public function index()
    {
        // Retorna todas las declaraciones con sus relaciones
        return Declaracion::with(['usuario', 'formulario', 'unidad', 'cargo'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_formulario' => 'required|exists:formulario,id_formulario',
            'id_unidad' => 'required|exists:unidad_academica,id_unidad',
            'id_cargo' => 'required|exists:cargo,id_cargo',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'horas_totales' => 'required|numeric|min:0',
        ]);

        $declaracion = Declaracion::create($validated);
        return response()->json($declaracion, 201);
    }

    public function show($id)
    {
        $declaracion = Declaracion::with(['usuario', 'formulario', 'unidad', 'cargo', 'horarios', 'documentos'])
            ->findOrFail($id);

        return response()->json($declaracion);
    }

    public function update(Request $request, $id)
    {
        $declaracion = Declaracion::findOrFail($id);
        $declaracion->update($request->all());
        return response()->json($declaracion);
    }

    public function destroy($id)
    {
        Declaracion::findOrFail($id)->delete();
        return response()->json(['message' => 'Declaración eliminada correctamente']);
    }
    public function usuario()
{
    return $this->belongsTo(Usuario::class, 'id_usuario');
}

public function formulario()
{
    return $this->belongsTo(Formulario::class, 'id_formulario');
}

public function unidad()
{
    return $this->belongsTo(UnidadAcademica::class, 'id_unidad');
}

public function cargo()
{
    return $this->belongsTo(Cargo::class, 'id_cargo');
}

public function horarios()
{
    return $this->hasMany(Horario::class, 'id_declaracion');
}

public function documentos()
{
    return $this->hasMany(Documento::class, 'id_declaracion');
}

}

