<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion, Usuario, UnidadAcademica, Cargo, Formulario, Horario};
use Illuminate\Http\Request;
use App\Notifications\DeclaracionGenerada;

class DeclaracionController extends Controller
{
    public function index()
    {
        try {
            $declaraciones = Declaracion::with(['usuario', 'unidad', 'cargo', 'formulario'])
                ->latest()
                ->get();

            return view('declaraciones.index', compact('declaraciones'));
        } catch (\Exception $e) {
            \Log::error('Error en DeclaracionController@index: ' . $e->getMessage());

            return view('declaraciones.index', [
                'declaraciones' => collect([]),
            ]);
        }
    }

    public function create()
    {
        return view('declaraciones.create', [
            'usuarios'   => Usuario::all(),
            'unidades'   => UnidadAcademica::with('sede')->get(),
            'cargos'     => Cargo::all(),
            'formularios'=> Formulario::all(),
            'jornadas'   => \App\Models\Jornada::orderBy('tipo')->get(),
            'horarios'   => Horario::whereNull('id_declaracion')
                                ->where('tipo', 'ucr')
                                ->with('jornada')
                                ->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'id_usuario'         => 'required|exists:usuario,id_usuario',
            'id_formulario'      => 'required|exists:formulario,id_formulario',
            'id_unidad'          => 'required|exists:unidad_academica,id_unidad',
            'id_cargo'           => 'required|exists:cargo,id_cargo',
            'fecha_desde'        => 'required|date',
            'fecha_hasta'        => 'required|date|after_or_equal:fecha_desde',
            'horas_totales'      => 'required|numeric|min:0',
            'ucr_dia.*'          => 'required|string',
            'ucr_hora_inicio.*'  => 'required',
            'ucr_hora_fin.*'     => 'required',
            'ext_institucion.*'  => 'nullable|string',
            'ext_dia.*'          => 'nullable|string',
            'ext_hora_inicio.*'  => 'nullable',
            'ext_hora_fin.*'     => 'nullable',
        ]);

        // Validar jornada
        $jornada = \App\Models\Jornada::find($r->id_jornada);
        if (!$jornada) {
            return back()->withInput()->withErrors([
                'jornada' => 'Debe seleccionar una jornada válida',
            ]);
        }

        if ($data['horas_totales'] != $jornada->horas_por_semana) {
            return back()->withInput()->withErrors([
                'horas' => 'Las horas totales deben coincidir con la jornada seleccionada',
            ]);
        }

        // Construir arreglo de horarios para validar conflictos
        $horarios = [];

        // Horarios externos
        if ($r->has('ext_dia')) {
            foreach ($r->ext_dia as $i => $dia) {
                if (empty($dia)) continue;

                $horarios[] = [
                    'tipo'   => 'externo',
                    'dia'    => $dia,
                    'inicio' => strtotime($r->ext_hora_inicio[$i]),
                    'fin'    => strtotime($r->ext_hora_fin[$i]),
                ];
            }
        }

        // Horarios UCR
        if ($r->has('ucr_dia')) {
            foreach ($r->ucr_dia as $i => $dia) {
                if (empty($dia)) continue;

                $horarios[] = [
                    'tipo'   => 'ucr',
                    'dia'    => $dia,
                    'inicio' => strtotime($r->ucr_hora_inicio[$i]),
                    'fin'    => strtotime($r->ucr_hora_fin[$i]),
                ];
            }
        }

        // Validar solapamientos y separación entre UCR/externo
        for ($i = 0; $i < count($horarios); $i++) {
            for ($j = $i + 1; $j < count($horarios); $j++) {
                $h1 = $horarios[$i];
                $h2 = $horarios[$j];

                if ($h1['dia'] !== $h2['dia']) {
                    continue;
                }

                // Solapamiento
                if ($h1['inicio'] < $h2['fin'] && $h1['fin'] > $h2['inicio']) {
                    return back()->withInput()->withErrors([
                        'horarios' => "Conflicto detectado en {$h1['dia']}: los horarios se solapan",
                    ]);
                }

                // Diferencia mínima 1h entre UCR y externo
                if ($h1['tipo'] !== $h2['tipo']) {
                    $minDiff = min(
                        abs($h1['fin'] - $h2['inicio']),
                        abs($h2['fin'] - $h1['inicio'])
                    );

                    if ($minDiff < 3600) { // 3600 seg = 1h
                        return back()->withInput()->withErrors([
                            'horarios' => "Debe haber al menos 1 hora entre horario UCR y externo en {$h1['dia']}",
                        ]);
                    }
                }
            }
        }

        // Validar rango de horas UCR y hora de almuerzo
        if ($r->has('ucr_dia')) {
            foreach ($r->ucr_hora_inicio as $i => $inicio) {
                if (empty($inicio) || empty($r->ucr_hora_fin[$i])) continue;

                $inicioMin = $this->horaAMinutos($inicio);
                $finMin    = $this->horaAMinutos($r->ucr_hora_fin[$i]);

                // 7:00 (420) a 21:00 (1260)
                if ($inicioMin < 420) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'No se pueden programar clases antes de las 7:00 AM',
                    ]);
                }

                if ($finMin > 1260) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'No se pueden programar clases después de las 21:00 (9:00 PM)',
                    ]);
                }

                // Bloque 12:00 - 13:00
                if (
                    ($inicioMin >= 720 && $inicioMin < 780) ||
                    ($finMin > 720 && $finMin <= 780)
                ) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'No se pueden programar clases entre 12:00 PM y 1:00 PM (hora de almuerzo)',
                    ]);
                }
            }
        }

        // Crear declaración
        $declaracion = Declaracion::create([
            'id_usuario'    => $data['id_usuario'],
            'id_formulario' => $data['id_formulario'],
            'id_unidad'     => $data['id_unidad'],
            'id_cargo'      => $data['id_cargo'],
            'fecha_desde'   => $data['fecha_desde'],
            'fecha_hasta'   => $data['fecha_hasta'],
            'horas_totales' => $data['horas_totales'],
            'fecha_envio'   => now(),
        ]);

        // Guardar horarios UCR
        if ($r->has('ucr_dia')) {
            foreach ($r->ucr_dia as $i => $dia) {
                if (empty($dia)) continue;

                Horario::create([
                    'id_declaracion' => $declaracion->id_declaracion,
                    'tipo'           => 'ucr',
                    'dia'            => $dia,
                    'hora_inicio'    => $r->ucr_hora_inicio[$i],
                    'hora_fin'       => $r->ucr_hora_fin[$i],
                ]);
            }
        }

        // Guardar horarios externos
        if ($r->has('ext_dia')) {
            foreach ($r->ext_dia as $i => $dia) {
                if (empty($dia)) continue;

                Horario::create([
                    'id_declaracion' => $declaracion->id_declaracion,
                    'tipo'           => 'externo',
                    'dia'            => $dia,
                    'hora_inicio'    => $r->ext_hora_inicio[$i],
                    'hora_fin'       => $r->ext_hora_fin[$i],
                    'lugar'          => $r->ext_institucion[$i] ?? null,
                ]);
            }
        }

        // 🔔 Notificación: correo + panel (Laravel Notifications)
        if ($declaracion->usuario) {
            $declaracion->usuario->notify(new DeclaracionGenerada($declaracion));
        }

        return redirect()
            ->route('declaraciones.show', $declaracion->id_declaracion)
            ->with('ok', 'Declaración creada correctamente');
    }

    public function show($id)
    {
        $declaracion = Declaracion::with([
                'usuario',
                'unidad.sede',
                'cargo',
                'formulario',
                'horarios',
            ])
            ->findOrFail($id);

        return view('declaraciones.show', compact('declaracion'));
    }

    public function edit($id)
    {
        $d = Declaracion::with('horarios')->findOrFail($id);

        return view('declaraciones.edit', [
            'd'          => $d,
            'usuarios'   => Usuario::all(),
            'unidades'   => UnidadAcademica::with('sede')->get(),
            'cargos'     => Cargo::all(),
            'formularios'=> Formulario::all(),
            'horarios'   => Horario::where(function ($q) use ($d) {
                                $q->whereNull('id_declaracion')
                                  ->orWhere('id_declaracion', $d->id_declaracion);
                            })
                            ->where('tipo', 'ucr')
                            ->with('jornada')
                            ->get(),
        ]);
    }

    public function update(Request $r, $id)
    {
        $d = Declaracion::findOrFail($id);

        $data = $r->validate([
            'id_usuario'   => 'required|exists:usuario,id_usuario',
            'id_formulario'=> 'required|exists:formulario,id_formulario',
            'id_unidad'    => 'required|exists:unidad_academica,id_unidad',
            'id_cargo'     => 'required|exists:cargo,id_cargo',
            'fecha_desde'  => 'required|date',
            'fecha_hasta'  => 'required|date|after_or_equal:fecha_desde',
            'horas_totales'=> 'nullable|numeric|min:0',
            'id_horario'   => 'nullable|exists:horario,id_horario',
        ]);

        // Ajustar horas_totales según jornada del horario seleccionado (si aplica)
        if (!empty($data['id_horario'])) {
            $hor = Horario::with('jornada')->find($data['id_horario']);
            if ($hor && $hor->jornada) {
                $data['horas_totales'] = $hor->jornada->horas_por_semana;
            }
        }

        $d->update($data);

        // Manejo del horario asociado
        if (array_key_exists('id_horario', $data)) {
            Horario::where('id_declaracion', $d->id_declaracion)
                ->where('id_horario', '<>', $data['id_horario'] ?? 0)
                ->update(['id_declaracion' => null]);

            if (!empty($data['id_horario']) && isset($hor)) {
                $hor->id_declaracion = $d->id_declaracion;
                $hor->save();
            }
        }

        return redirect()
            ->route('declaraciones.index')
            ->with('ok', 'Declaración actualizada');
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
    public function destroy($id)
    {
        Declaracion::findOrFail($id)->delete();

        return back()->with('ok', 'Declaración eliminada');
    }

    // Helper: HH:mm a minutos
    private function horaAMinutos($hora)
    {
        [$h, $m] = explode(':', $hora);
        return intval($h) * 60 + intval($m);
    }
}
