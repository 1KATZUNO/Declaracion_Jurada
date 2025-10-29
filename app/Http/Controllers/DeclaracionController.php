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
        'horas_totales' => 'required|numeric|min:0',

        // validamos arrays
        'tipo.*' => 'nullable|in:ucr,externo',
        'dia.*' => 'nullable|string',
        'hora_inicio.*' => 'nullable',
        'hora_fin.*' => 'nullable',
        'lugar.*' => 'nullable|string|max:255',
    ]);

    // Construimos array temporal de horarios para validar solapamientos
    $items = [];
    if ($r->has('tipo')) {
        foreach ($r->tipo as $i => $tipo) {
            $dia = $r->input("dia.$i");
            $hi = $r->input("hora_inicio.$i");
            $hf = $r->input("hora_fin.$i");
            $lugar = $r->input("lugar.$i");

            // Si es fila vacía (sin tipo o sin día), ignorar
            if (!$tipo || (!$lugar && !$dia && !$hi && !$hf)) continue;

            // Normalizar: si tipo externo y no tiene horas, permitimos guardar con hora null
            $items[] = [
                'tipo' => $tipo,
                'dia' => $dia,
                'hora_inicio' => $hi,
                'hora_fin' => $hf,
                'lugar' => $lugar,
            ];
        }
    }

    // Verificar solapamientos: para cada día, recoger intervalos con horas y comprobar
    $intervalsByDay = [];
    foreach ($items as $it) {
        if (!empty($it['hora_inicio']) && !empty($it['hora_fin']) && !empty($it['dia'])) {
            $start = strtotime($it['hora_inicio']);
            $end = strtotime($it['hora_fin']);
            if ($end <= $start) {
                return back()->withInput()->withErrors(['horarios' => "Hora fin debe ser mayor que hora inicio para el día {$it['dia']}."]);
            }
            $intervalsByDay[$it['dia']][] = ['start' => $start, 'end' => $end, 'tipo' => $it['tipo'], 'lugar' => $it['lugar'] ?? null];
        }
    }

    // Función simple para detectar overlap
    foreach ($intervalsByDay as $dia => $intervals) {
        usort($intervals, function($a,$b){ return $a['start'] <=> $b['start']; });
        $prevEnd = null;
        foreach ($intervals as $int) {
            if ($prevEnd !== null && $int['start'] < $prevEnd) {
                return back()->withInput()->withErrors(['horarios' => "Solapamiento detectado en $dia entre horarios (no está permitido)."]);
            }
            $prevEnd = $int['end'];
        }
    }

    // Crear la declaración
    $declaracion = Declaracion::create($data + ['fecha_envio' => now()]);

    // Persistir horarios
    foreach ($items as $it) {
        // Evita crear filas vacías (si no tiene día ni horas y es externo con solo lugar, aún guardamos)
        if (empty($it['dia']) && empty($it['hora_inicio']) && empty($it['hora_fin']) && empty($it['lugar'])) continue;

        Horario::create([
            'id_declaracion' => $declaracion->id_declaracion,
            'tipo' => $it['tipo'],
            'dia' => $it['dia'] ?? null,
            'hora_inicio' => $it['hora_inicio'] ?? null,
            'hora_fin' => $it['hora_fin'] ?? null,
            'lugar' => $it['lugar'] ?? null,
        ]);
    }

    return redirect()
        ->route('declaraciones.index')
        ->with('ok', 'Declaración creada correctamente con horarios.');
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

