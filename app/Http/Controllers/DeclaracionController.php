<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion,Usuario,UnidadAcademica,Cargo,Formulario,Horario};
use Illuminate\Http\Request;

class DeclaracionController extends Controller
{
    public function index(){
        try {
            $declaraciones = Declaracion::with(['usuario','unidad','cargo','formulario'])->latest()->get();
            return view('declaraciones.index', compact('declaraciones'));
        } catch (\Exception $e) {
            \Log::error('Error en DeclaracionController@index: ' . $e->getMessage());
            
            // Retornar vista con colección vacía en caso de error
            return view('declaraciones.index', [
                'declaraciones' => collect([])
            ]);
        }
    }

    public function create(){
        return view('declaraciones.create', [
            'usuarios'=>Usuario::all(),
            'unidades'=>UnidadAcademica::with('sede')->get(),
            'cargos'=>Cargo::all(),
            'formularios'=>Formulario::all(),
            // horarios disponibles: tipo UCR y aún no asignados a una declaración
            'horarios'=> \App\Models\Horario::whereNull('id_declaracion')->where('tipo','ucr')->with('jornada')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_formulario' => 'required|exists:formulario,id_formulario',
            'id_unidad' => 'required|exists:unidad_academica,id_unidad',
            'id_cargo' => 'required|exists:cargo,id_cargo',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'horas_totales' => 'nullable|numeric|min:0',
            'id_horario' => 'nullable|exists:horario,id_horario',
        ]);

        // Si seleccionó un horario, tomar las horas desde la jornada asociada (si existe)
        if (!empty($data['id_horario'])) {
            $hor = Horario::with('jornada')->find($data['id_horario']);
            if ($hor && $hor->jornada) {
                $data['horas_totales'] = $hor->jornada->horas_por_semana;
            }
        }

        // Asegurar valor
        if (!isset($data['horas_totales'])) $data['horas_totales'] = 0;

        $declaracion = Declaracion::create($data + ['fecha_envio' => now()]);

        // Si llegó id_horario, asignarlo a la declaración (actualiza el horario existente)
        if (!empty($data['id_horario'])) {
            $hor->id_declaracion = $declaracion->id_declaracion;
            $hor->save();
        }

        return redirect()
            ->route('declaraciones.show', $declaracion->id_declaracion)
            ->with('ok', 'Declaración creada correctamente. Ahora puede gestionar horarios desde el módulo "Horarios".');
    }

    public function show($id){
        $declaracion = Declaracion::with(['usuario','unidad.sede','cargo','formulario','horarios'])->findOrFail($id);
        return view('declaraciones.show', compact('declaracion'));
    }

    public function edit($id){
        $d = Declaracion::with('horarios')->findOrFail($id);
        return view('declaraciones.edit', [
            'd'=>$d,
            'usuarios'=>Usuario::all(),
            'unidades'=>UnidadAcademica::with('sede')->get(),
            'cargos'=>Cargo::all(),
            'formularios'=>Formulario::all(),
            // incluir horarios disponibles + permitir seleccionar el que ya está asignado (si existe)
            'horarios'=> \App\Models\Horario::where(function($q) use ($d){
                $q->whereNull('id_declaracion')->orWhere('id_declaracion', $d->id_declaracion);
            })->where('tipo','ucr')->with('jornada')->get(),
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
            'horas_totales'=>'nullable|numeric|min:0',
            'id_horario' => 'nullable|exists:horario,id_horario',
        ]);

        // Si seleccionó un horario, fijar horas desde la jornada asociada
        if (!empty($data['id_horario'])) {
            $hor = Horario::with('jornada')->find($data['id_horario']);
            if ($hor && $hor->jornada) {
                $data['horas_totales'] = $hor->jornada->horas_por_semana;
            }
        }

        // Actualizar datos generales
        $d->update($data);

        // Asociar/desasociar horarios si se seleccionó uno
        if (array_key_exists('id_horario', $data)) {
            // desasignar otros horarios actualmente vinculados a esta declaración
            \App\Models\Horario::where('id_declaracion', $d->id_declaracion)
                ->where('id_horario', '<>', $data['id_horario'] ?? 0)
                ->update(['id_declaracion' => null]);

            if (!empty($data['id_horario'])) {
                $hor->id_declaracion = $d->id_declaracion;
                $hor->save();
            }
        }

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

