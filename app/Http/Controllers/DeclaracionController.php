<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion,Usuario,UnidadAcademica,Cargo,Formulario,Horario};
use Illuminate\Http\Request;

class DeclaracionController extends Controller
{
    public function index(){
        $declaraciones = Declaracion::with(['usuario','unidad','cargo','formulario'])->latest()->get();
        return view('declaraciones.index', compact('declaraciones'));
    }

    public function create(){
        return view('declaraciones.create', [
            'usuarios'=>Usuario::all(),
            'unidades'=>UnidadAcademica::with('sede')->get(),
            'cargos'=>Cargo::all(),
            'formularios'=>Formulario::all(),
        ]);
    }

    public function store(Request $r){
        $data = $r->validate([
            'id_usuario'=>'required|exists:usuario,id_usuario',
            'id_formulario'=>'required|exists:formulario,id_formulario',
            'id_unidad'=>'required|exists:unidad_academica,id_unidad',
            'id_cargo'=>'required|exists:cargo,id_cargo',
            'fecha_desde'=>'required|date',
            'fecha_hasta'=>'required|date|after_or_equal:fecha_desde',
            'horas_totales'=>'required|numeric|min:0',

            // arrays de horarios
            'tipo.*' => 'required|in:ucr,externo',
            'dia.*' => 'required|string',
            'hora_inicio.*' => 'required',
            'hora_fin.*' => 'required',
        ]);

        $declaracion = Declaracion::create($data + ['fecha_envio'=>now()]);

        if ($r->has('tipo')) {
            foreach ($r->tipo as $i => $tipo) {
                Horario::create([
                    'id_declaracion' => $declaracion->id_declaracion,
                    'tipo' => $tipo,
                    'dia' => $r->dia[$i],
                    'hora_inicio' => $r->hora_inicio[$i],
                    'hora_fin' => $r->hora_fin[$i],
                ]);
            }
        }

        return redirect()->route('declaraciones.index')->with('ok','Declaración creada correctamente');
    }

    public function show($id){
        $d = Declaracion::with(['usuario','unidad.sede','cargo','formulario','horarios'])->findOrFail($id);
        return view('declaraciones.show', compact('d'));
    }

    public function edit($id){
        $d = Declaracion::with('horarios')->findOrFail($id);
        return view('declaraciones.edit', [
            'd'=>$d,
            'usuarios'=>Usuario::all(),
            'unidades'=>UnidadAcademica::with('sede')->get(),
            'cargos'=>Cargo::all(),
            'formularios'=>Formulario::all(),
        ]);
    }

    // Opción A: actualizar solo los campos de la declaración
    public function update(Request $r,$id){
        $d = Declaracion::findOrFail($id);
        $data = $r->validate([
            'id_usuario'=>'required|exists:usuario,id_usuario',
            'id_formulario'=>'required|exists:formulario,id_formulario',
            'id_unidad'=>'required|exists:unidad_academica,id_unidad',
            'id_cargo'=>'required|exists:cargo,id_cargo',
            'fecha_desde'=>'required|date',
            'fecha_hasta'=>'required|date|after_or_equal:fecha_desde',
            'horas_totales'=>'required|numeric|min:0'
        ]);
        $d->update($data);
        return redirect()->route('declaraciones.index')->with('ok','Declaración actualizada');
    }

    // --- Opción B (si quieres sincronizar horarios al editar) ---
    // public function update(Request $r,$id){
    //     $d = Declaracion::findOrFail($id);
    //     $data = $r->validate([
    //         'id_usuario'=>'required|exists:usuario,id_usuario',
    //         'id_formulario'=>'required|exists:formulario,id_formulario',
    //         'id_unidad'=>'required|exists:unidad_academica,id_unidad',
    //         'id_cargo'=>'required|exists:cargo,id_cargo',
    //         'fecha_desde'=>'required|date',
    //         'fecha_hasta'=>'required|date|after_or_equal:fecha_desde',
    //         'horas_totales'=>'required|numeric|min:0',
    //         'tipo.*' => 'nullable|in:ucr,externo',
    //         'dia.*' => 'nullable|string',
    //         'hora_inicio.*' => 'nullable',
    //         'hora_fin.*' => 'nullable',
    //     ]);
    //     $d->update($data);

    //     // reset horarios y volver a crear según formulario
    //     $d->horarios()->delete();
    //     if ($r->has('tipo')) {
    //         foreach ($r->tipo as $i => $tipo) {
    //             if (!$tipo) continue;
    //             Horario::create([
    //                 'id_declaracion' => $d->id_declaracion,
    //                 'tipo' => $tipo,
    //                 'dia' => $r->dia[$i],
    //                 'hora_inicio' => $r->hora_inicio[$i],
    //                 'hora_fin' => $r->hora_fin[$i],
    //             ]);
    //         }
    //     }

    //     return redirect()->route('declaraciones.index')->with('ok','Declaración + horarios actualizados');
    // }

    public function destroy($id){
        Declaracion::findOrFail($id)->delete();
        return back()->with('ok','Declaración eliminada');
    }
}

