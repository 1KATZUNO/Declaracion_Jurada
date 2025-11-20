<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion, Usuario, UnidadAcademica, Cargo, Formulario, Horario, Sede};
use Illuminate\Http\Request;
use App\Notifications\DeclaracionGenerada;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\Log;

class DeclaracionController extends Controller
{
    public function index()
    {
        try {
            // Optimización: Usar paginación para mejorar rendimiento
            $declaraciones = Declaracion::with(['usuario:id_usuario,nombre,apellido,correo', 
                                                'unidad:id_unidad,nombre', 
                                                'cargo:id_cargo,nombre', 
                                                'formulario:id_formulario,titulo'])
                ->latest()
                ->paginate(20);

            return view('declaraciones.index', compact('declaraciones'));
        } catch (\Exception $e) {
            \Log::error('Error en DeclaracionController@index: ' . $e->getMessage());

            return view('declaraciones.index', [
                'declaraciones' => collect([]),
            ]);
        }
    }

    public function create(Request $request)
    {
        // Obtener usuario actual (mismo enfoque que layout.blade.php)
        $usuarioActual = null;
        if (function_exists('auth') && auth()->check()) {
            $usuarioActual = auth()->user();
        } elseif ($request->session()->has('usuario_id')) {
            $usuarioActual = Usuario::find($request->session()->get('usuario_id'));
        }

        // Obtener nombre del usuario (prioridad a sesión como en layout)
        $nombreUsuario = session('usuario_nombre')
            ?? ($usuarioActual
                ? trim(($usuarioActual->nombre ?? '') . ' ' . ($usuarioActual->apellido ?? ''))
                : 'Usuario no identificado');

        return view('declaraciones.create', [
            'usuarios'   => Usuario::select('id_usuario', 'nombre', 'apellido', 'correo', 'identificacion', 'telefono')->orderBy('nombre')->get(),
            'usuarioActual' => $usuarioActual,
            'nombreUsuario' => $nombreUsuario,
            'sedes'      => Sede::select('id_sede', 'nombre')->orderBy('nombre')->get(),
            'unidades'   => UnidadAcademica::with('sede:id_sede,nombre')->select('id_unidad', 'nombre', 'id_sede')->get(),
            'cargos'     => Cargo::select('id_cargo', 'nombre')->orderBy('nombre')->get(),
            'formularios'=> Formulario::select('id_formulario', 'titulo')->get(),
            'jornadas'   => \App\Models\Jornada::select('id_jornada', 'tipo', 'horas_por_semana')->orderBy('tipo')->get(),
            'horarios'   => Horario::whereNull('id_declaracion')
                                ->where('tipo', 'ucr')
                                ->with('jornada:id_jornada,tipo,horas_por_semana')
                                ->select('id_horario', 'id_jornada', 'tipo')
                                ->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_formulario' => 'required|exists:formulario,id_formulario',
            'id_unidad' => 'required|exists:unidad_academica,id_unidad',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
            'horas_totales' => 'nullable|numeric|min:0',
            'id_jornada_externo' => 'nullable|exists:jornada,id_jornada',
            // Validación de horarios
            'ucr_dia.*' => 'required|string',
            'ucr_hora_inicio.*' => 'required',
            'ucr_hora_fin.*' => 'required',
            'ext_institucion.*' => 'nullable|string',
            'ext_dia.*' => 'nullable|string',
            'ext_hora_inicio.*' => 'nullable',
            'ext_hora_fin.*' => 'nullable',
            'ext_fecha_desde.*' => 'nullable|date',
            'ext_fecha_hasta.*' => 'nullable|date',
        ]);

        // Validar cada cargo UCR individualmente
        if ($r->has('ucr_jornada') && $r->has('ucr_cargo_index')) {
            // Agrupar datos por cargo UCR
            $cargosPorIndex = [];
            
            // Agrupar ucr_cargo y ucr_jornada por índice
            foreach ($r->ucr_jornada as $idx => $jornadaId) {
                if (empty($jornadaId)) continue;
                
                $cargoId = isset($r->ucr_cargo[$idx]) ? $r->ucr_cargo[$idx] : null;
                $cargo = $cargoId ? \App\Models\Cargo::find($cargoId) : null;
                $nombreCargo = $cargo ? $cargo->nombre : "Cargo " . ($idx + 1);
                
                if (!isset($cargosPorIndex[$idx])) {
                    $cargosPorIndex[$idx] = [
                        'nombre' => $nombreCargo,
                        'cargo_id' => $cargoId,
                        'jornada_id' => $jornadaId,
                        'horas' => 0
                    ];
                }
            }
            
            // Sumar las horas de cada horario a su cargo correspondiente
            foreach ($r->ucr_cargo_index as $i => $cargoIndex) {
                if (!isset($cargosPorIndex[$cargoIndex])) continue;
                
                $inicio = $r->ucr_hora_inicio[$i] ?? null;
                $fin = $r->ucr_hora_fin[$i] ?? null;
                
                if (!empty($inicio) && !empty($fin)) {
                    $inicioTime = strtotime($inicio);
                    $finTime = strtotime($fin);
                    $cargosPorIndex[$cargoIndex]['horas'] += ($finTime - $inicioTime) / 3600;
                }
            }
            
            // Validar cada cargo
            foreach ($cargosPorIndex as $idx => $cargoData) {
                $jornadaUCR = \App\Models\Jornada::find($cargoData['jornada_id']);
                if (!$jornadaUCR) continue;
                
                $horasCargo = round($cargoData['horas'], 1);
                $horasRequeridas = $jornadaUCR->horas_por_semana;
                
                if ($horasCargo != $horasRequeridas) {
                    $diferenciaCargo = $horasCargo - $horasRequeridas;
                    if ($diferenciaCargo > 0) {
                        $mensaje = "Cargo UCR \"" . $cargoData['nombre'] . "\": EXCEDE la jornada por " . abs($diferenciaCargo) . " horas. Asignadas: " . $horasCargo . "h | Requeridas: " . $horasRequeridas . "h";
                    } else {
                        $mensaje = "Cargo UCR \"" . $cargoData['nombre'] . "\": FALTAN " . abs($diferenciaCargo) . " horas para completar la jornada. Asignadas: " . $horasCargo . "h | Requeridas: " . $horasRequeridas . "h";
                    }
                    return back()->withInput()->withErrors(['horas_ucr' => $mensaje]);
                }
            }
        }

        // Validar cada institución externa individualmente
        if ($r->has('ext_jornada') && $r->has('ext_inst_index')) {
            // Primero, agrupar datos por institución
            $institucionesPorIndex = [];
            
            // Agrupar ext_institucion y ext_jornada por índice
            foreach ($r->ext_jornada as $idx => $jornadaId) {
                if (empty($jornadaId)) continue;
                
                $nombreInstitucion = isset($r->ext_institucion[$idx]) ? $r->ext_institucion[$idx] : "Institución " . ($idx + 1);
                
                if (!isset($institucionesPorIndex[$idx])) {
                    $institucionesPorIndex[$idx] = [
                        'nombre' => $nombreInstitucion,
                        'jornada_id' => $jornadaId,
                        'horas' => 0
                    ];
                }
            }
            
            // Ahora sumar las horas de cada horario a su institución correspondiente
            foreach ($r->ext_inst_index as $i => $instIndex) {
                if (!isset($institucionesPorIndex[$instIndex])) continue;
                
                $inicio = $r->ext_hora_inicio[$i] ?? null;
                $fin = $r->ext_hora_fin[$i] ?? null;
                
                if (!empty($inicio) && !empty($fin)) {
                    $inicioTime = strtotime($inicio);
                    $finTime = strtotime($fin);
                    $institucionesPorIndex[$instIndex]['horas'] += ($finTime - $inicioTime) / 3600;
                }
            }
            
            // Validar cada institución
            foreach ($institucionesPorIndex as $idx => $instData) {
                $jornadaExterno = \App\Models\Jornada::find($instData['jornada_id']);
                if (!$jornadaExterno) continue;
                
                $horasInstitucion = round($instData['horas'], 1);
                $horasRequeridas = $jornadaExterno->horas_por_semana;
                
                if ($horasInstitucion != $horasRequeridas) {
                    $diferenciaExt = $horasInstitucion - $horasRequeridas;
                    if ($diferenciaExt > 0) {
                        $mensajeExt = "Institución \"" . $instData['nombre'] . "\": EXCEDE la jornada por " . abs($diferenciaExt) . " horas. Asignadas: " . $horasInstitucion . "h | Requeridas: " . $horasRequeridas . "h";
                    } else {
                        $mensajeExt = "Institución \"" . $instData['nombre'] . "\": FALTAN " . abs($diferenciaExt) . " horas para completar la jornada. Asignadas: " . $horasInstitucion . "h | Requeridas: " . $horasRequeridas . "h";
                    }
                    return back()->withInput()->withErrors(['horas_externas' => $mensajeExt]);
                }
            }
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

                // Si son del mismo tipo (ambos UCR o ambos externos): no pueden solaparse
                if ($h1['tipo'] === $h2['tipo']) {
                    if ($h1['inicio'] < $h2['fin'] && $h1['fin'] > $h2['inicio']) {
                        return back()->withInput()->withErrors([
                            'horarios' => "Conflicto detectado en {$h1['dia']}: los horarios {$h1['tipo']} se solapan",
                        ]);
                    }
                }

                // Si son de diferente tipo (UCR vs externo): no pueden solaparse Y debe haber 1h de diferencia
                if ($h1['tipo'] !== $h2['tipo']) {
                    // Primero verificar que no se solapen
                    if ($h1['inicio'] < $h2['fin'] && $h1['fin'] > $h2['inicio']) {
                        return back()->withInput()->withErrors([
                            'horarios' => "Conflicto detectado en {$h1['dia']}: los horarios UCR y externos no pueden solaparse",
                        ]);
                    }
                    
                    // Luego verificar que haya mínimo 1 hora de diferencia
                    // Calcular la distancia entre el fin de uno y el inicio del otro
                    $distancia = 0;
                    if ($h1['fin'] <= $h2['inicio']) {
                        // h1 termina antes que h2 comienza
                        $distancia = $h2['inicio'] - $h1['fin'];
                    } else if ($h2['fin'] <= $h1['inicio']) {
                        // h2 termina antes que h1 comienza
                        $distancia = $h1['inicio'] - $h2['fin'];
                    }
                    
                    if ($distancia < 3600) { // 3600 seg = 1h
                        return back()->withInput()->withErrors([
                            'horarios' => "Debe haber al menos 1 hora de diferencia entre horarios UCR y externos en {$h1['dia']}",
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
                        'horarios' => 'Los horarios de la UCR deben iniciar mínimo a las 7:00 AM',
                    ]);
                }

                if ($finMin > 1260) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'Los horarios de la UCR deben finalizar máximo a las 21:00 (9:00 PM)',
                    ]);
                }

                // Bloque 12:01 - 12:59 (hora de almuerzo)
                // No se puede iniciar ni finalizar en este rango
                if (
                    ($inicioMin > 720 && $inicioMin < 780) ||
                    ($finMin > 720 && $finMin < 780)
                ) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'No se pueden crear horarios entre las 12:01 PM y 12:59 PM (hora de almuerzo)',
                    ]);
                }
                
                // Tampoco puede abarcar el periodo de almuerzo
                if ($inicioMin <= 720 && $finMin >= 780) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'Los horarios no pueden abarcar la hora de almuerzo (12:01-12:59). Divida el horario en dos bloques.',
                    ]);
                }
            }
        }

        // Si todo está bien, crear la declaración y sus horarios
        // Calcular horas totales de todos los cargos UCR
        $horasTotales = 0;
        if ($r->has('ucr_jornada')) {
            foreach ($r->ucr_jornada as $jornadaId) {
                if (!empty($jornadaId)) {
                    $jornadaUCR = \App\Models\Jornada::find($jornadaId);
                    if ($jornadaUCR) {
                        $horasTotales += $jornadaUCR->horas_por_semana;
                    }
                }
            }
        }
        
        // Obtener el primer cargo UCR como cargo principal de la declaración
        $cargoPrincipal = null;
        if ($r->has('ucr_cargo')) {
            foreach ($r->ucr_cargo as $cargoId) {
                if (!empty($cargoId)) {
                    $cargoPrincipal = $cargoId;
                    break;
                }
            }
        }

        // Determinar las fechas mínimas y máximas de los cargos UCR
        $fechaDesdeGlobal = null;
        $fechaHastaGlobal = null;

        if ($r->has('ucr_cargo_fecha_desde') && $r->has('ucr_cargo_fecha_hasta')) {
            // Filtrar solo las fechas válidas (no vacías)
            $fechasDesde = array_filter($r->ucr_cargo_fecha_desde, fn($f) => !empty($f));
            $fechasHasta = array_filter($r->ucr_cargo_fecha_hasta, fn($f) => !empty($f));

            if (count($fechasDesde) > 0) {
                $fechaDesdeGlobal = min($fechasDesde);
            }
            if (count($fechasHasta) > 0) {
                $fechaHastaGlobal = max($fechasHasta);
            }
        }

        // Crear la declaración
        $usuario = Usuario::find($data['id_usuario']);

        $declaracion = Declaracion::create([
            'id_usuario' => $data['id_usuario'],
            'id_formulario' => $data['id_formulario'],
            'id_unidad' => $data['id_unidad'],
            'id_cargo' => $cargoPrincipal,
            'identificacion' => $usuario->identificacion ?? null,
            'telefono' => $usuario->telefono ?? null,
            'fecha_desde' => $fechaDesdeGlobal,
            'fecha_hasta' => $fechaHastaGlobal,
            'horas_totales' => $horasTotales,
            'fecha_envio' => \Carbon\Carbon::now('America/Costa_Rica'),
            'observaciones_adicionales' => $r->observaciones_adicionales ?? null,
        ]);


        // Guardar horarios UCR
        if ($r->has('ucr_dia')) {
            foreach ($r->ucr_dia as $i => $dia) {
                if (empty($dia)) continue;
                
                // Obtener el índice del cargo para este horario
                $cargoIndex = isset($r->ucr_cargo_index[$i]) ? $r->ucr_cargo_index[$i] : 0;
                $cargoId = isset($r->ucr_cargo[$cargoIndex]) && !empty($r->ucr_cargo[$cargoIndex]) ? $r->ucr_cargo[$cargoIndex] : null;
                
                // Obtener jornada del cargo
                $jornadaId = isset($r->ucr_jornada[$cargoIndex]) && !empty($r->ucr_jornada[$cargoIndex]) ? $r->ucr_jornada[$cargoIndex] : null;
                
                // Obtener fechas del cargo (fijas para todo el cargo)
                $fechaDesde = isset($r->ucr_cargo_fecha_desde[$cargoIndex]) ? $r->ucr_cargo_fecha_desde[$cargoIndex] : null;
                $fechaHasta = isset($r->ucr_cargo_fecha_hasta[$cargoIndex]) ? $r->ucr_cargo_fecha_hasta[$cargoIndex] : null;
                
                Horario::create([
                    'id_declaracion' => $declaracion->id_declaracion,
                    'id_cargo' => $cargoId,
                    'id_jornada' => $jornadaId,
                    'tipo' => 'ucr',
                    'dia' => $dia,
                    'hora_inicio' => $r->ucr_hora_inicio[$i],
                    'hora_fin' => $r->ucr_hora_fin[$i],
                    'desde' => $fechaDesde,
                    'hasta' => $fechaHasta,
                ]);
            }
        }

        // Guardar horarios externos
        if ($r->has('ext_dia') && $r->has('ext_inst_index')) {
            foreach ($r->ext_dia as $i => $dia) {
                if (empty($dia)) continue;
                
                // Obtener el índice de institución para este horario
                $instIndex = $r->ext_inst_index[$i];
                
                // Obtener el id_jornada, nombre de institución y cargo del índice correspondiente
                $jornadaId = isset($r->ext_jornada[$instIndex]) ? $r->ext_jornada[$instIndex] : null;
                $nombreInstitucion = isset($r->ext_institucion[$instIndex]) ? $r->ext_institucion[$instIndex] : null;
                $cargoExterno = isset($r->ext_cargo[$instIndex]) ? $r->ext_cargo[$instIndex] : null;
                
                // Obtener fechas de la institución (fijas para toda la institución)
                $fechaDesde = isset($r->ext_inst_fecha_desde[$instIndex]) ? $r->ext_inst_fecha_desde[$instIndex] : null;
                $fechaHasta = isset($r->ext_inst_fecha_hasta[$instIndex]) ? $r->ext_inst_fecha_hasta[$instIndex] : null;
                
                Horario::create([
                    'id_declaracion' => $declaracion->id_declaracion,
                    'id_jornada' => $jornadaId,
                    'tipo' => 'externo',
                    'dia' => $dia,
                    'hora_inicio' => $r->ext_hora_inicio[$i],
                    'hora_fin' => $r->ext_hora_fin[$i],
                    'lugar' => $nombreInstitucion,
                    'cargo' => $cargoExterno,
                    'desde' => $fechaDesde,
                    'hasta' => $fechaHasta,
                ]);
            }
        }

        //🔔 Notificación: Sistema completo (BD + Email)
        $notificacionService = new NotificacionService();
        $notificacionService->notificarCrearDeclaracion($declaracion);

        return redirect()
            ->route('declaraciones.show', $declaracion->id_declaracion)
            ->with('ok', 'Declaración creada correctamente')
            ->with('refresh_notifications', true);
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

    public function edit(Request $request, $id)
    {
        $d = Declaracion::with([
            'horarios.jornada:id_jornada,tipo,horas_por_semana',
            'cargo:id_cargo,nombre'
        ])->findOrFail($id);

        // Obtener usuario actual
        $usuarioActual = null;
        if (function_exists('auth') && auth()->check()) {
            $usuarioActual = auth()->user();
        } elseif ($request->session()->has('usuario_id')) {
            $usuarioActual = Usuario::find($request->session()->get('usuario_id'));
        }
        
        // Obtener el nombre del usuario para mostrar en el formulario
        $nombreUsuario = '';
        if ($request->session()->has('usuario_nombre') && !empty($request->session()->get('usuario_nombre'))) {
            $nombreUsuario = $request->session()->get('usuario_nombre');
        } elseif ($usuarioActual && !empty($usuarioActual->nombre)) {
            $nombreUsuario = $usuarioActual->nombre;
        }

        // Obtener la jornada del primer horario UCR si existe
        $horarioUCR = $d->horarios->where('tipo', 'ucr')->first();
        $jornadaActual = null;

        if ($horarioUCR && $horarioUCR->id_jornada) {
            // Si el horario tiene jornada asignada, usarla
            $jornadaActual = $horarioUCR->jornada;
        } else {
            // Si no tiene jornada asignada, intentar deducir por las horas totales
            $horasTotales = $d->horas_totales;
            if ($horasTotales) {
                $jornadas = \App\Models\Jornada::select('id_jornada', 'tipo', 'horas_por_semana')->get();
                $jornadaActual = $jornadas->sortBy(function($jornada) use ($horasTotales) {
                    return abs($jornada->horas_por_semana - $horasTotales);
                })->first();
            }
        }



        return view('declaraciones.edit', [
            'd'                => $d,
            'jornadaActual'    => $jornadaActual,
            'usuarios'         => Usuario::select('id_usuario', 'nombre', 'apellido', 'correo', 'identificacion', 'telefono')->orderBy('nombre')->get(),
            'usuarioActual'    => $usuarioActual,
            'nombreUsuario'    => $nombreUsuario,
            'sedes'            => Sede::select('id_sede', 'nombre')->orderBy('nombre')->get(),
            'unidades'         => UnidadAcademica::with('sede:id_sede,nombre')->select('id_unidad', 'nombre', 'id_sede')->get(),
            'cargos'           => Cargo::select('id_cargo', 'nombre')->orderBy('nombre')->get(),
            'formularios'      => Formulario::select('id_formulario', 'titulo')->get(),
            'jornadas'         => \App\Models\Jornada::select('id_jornada', 'tipo', 'horas_por_semana')->orderBy('tipo')->get(),
            'horarios'         => Horario::whereNull('id_declaracion')
                                    ->where('tipo', 'ucr')
                                    ->with('jornada')
                                    ->get(),
        ]);
    }

    public function update(Request $r, $id)
    {
        $d = Declaracion::findOrFail($id);

        $data = $r->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_formulario' => 'required|exists:formulario,id_formulario',
            'id_unidad' => 'required|exists:unidad_academica,id_unidad',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
            'horas_totales' => 'nullable|numeric|min:0',
            'id_jornada_externo' => 'nullable|exists:jornada,id_jornada',
            // Validación de horarios
            'ucr_dia.*' => 'required|string',
            'ucr_hora_inicio.*' => 'required',
            'ucr_hora_fin.*' => 'required',
            'ext_institucion.*' => 'nullable|string',
            'ext_dia.*' => 'nullable|string',
            'ext_hora_inicio.*' => 'nullable',
            'ext_hora_fin.*' => 'nullable',
            'ext_fecha_desde.*' => 'nullable|date',
            'ext_fecha_hasta.*' => 'nullable|date',
        ]);

        // Validar cada cargo UCR individualmente
        if ($r->has('ucr_jornada') && $r->has('ucr_cargo_index')) {
            // Agrupar datos por cargo UCR
            $cargosPorIndex = [];
            
            // Agrupar ucr_cargo y ucr_jornada por índice
            foreach ($r->ucr_jornada as $idx => $jornadaId) {
                if (empty($jornadaId)) continue;
                
                $cargoId = isset($r->ucr_cargo[$idx]) ? $r->ucr_cargo[$idx] : null;
                $cargo = $cargoId ? \App\Models\Cargo::find($cargoId) : null;
                $nombreCargo = $cargo ? $cargo->nombre : "Cargo " . ($idx + 1);
                
                if (!isset($cargosPorIndex[$idx])) {
                    $cargosPorIndex[$idx] = [
                        'nombre' => $nombreCargo,
                        'cargo_id' => $cargoId,
                        'jornada_id' => $jornadaId,
                        'horas' => 0
                    ];
                }
            }
            
            // Sumar las horas de cada horario a su cargo correspondiente
            foreach ($r->ucr_cargo_index as $i => $cargoIndex) {
                if (!isset($cargosPorIndex[$cargoIndex])) continue;
                
                $inicio = $r->ucr_hora_inicio[$i] ?? null;
                $fin = $r->ucr_hora_fin[$i] ?? null;
                
                if (!empty($inicio) && !empty($fin)) {
                    $inicioTime = strtotime($inicio);
                    $finTime = strtotime($fin);
                    $cargosPorIndex[$cargoIndex]['horas'] += ($finTime - $inicioTime) / 3600;
                }
            }
            
            // Validar cada cargo
            foreach ($cargosPorIndex as $idx => $cargoData) {
                $jornadaUCR = \App\Models\Jornada::find($cargoData['jornada_id']);
                if (!$jornadaUCR) continue;
                
                $horasCargo = round($cargoData['horas'], 1);
                $horasRequeridas = $jornadaUCR->horas_por_semana;
                
                if ($horasCargo != $horasRequeridas) {
                    $diferenciaCargo = $horasCargo - $horasRequeridas;
                    if ($diferenciaCargo > 0) {
                        $mensaje = "Cargo UCR \"" . $cargoData['nombre'] . "\": EXCEDE la jornada por " . abs($diferenciaCargo) . " horas. Asignadas: " . $horasCargo . "h | Requeridas: " . $horasRequeridas . "h";
                    } else {
                        $mensaje = "Cargo UCR \"" . $cargoData['nombre'] . "\": FALTAN " . abs($diferenciaCargo) . " horas para completar la jornada. Asignadas: " . $horasCargo . "h | Requeridas: " . $horasRequeridas . "h";
                    }
                    return back()->withInput()->withErrors(['horas_ucr' => $mensaje]);
                }
            }
        }

        // Validar cada institución externa individualmente
        if ($r->has('ext_jornada') && $r->has('ext_inst_index')) {
            // Primero, agrupar datos por institución
            $institucionesPorIndex = [];
            
            // Agrupar ext_institucion y ext_jornada por índice
            foreach ($r->ext_jornada as $idx => $jornadaId) {
                if (empty($jornadaId)) continue;
                
                $nombreInstitucion = isset($r->ext_institucion[$idx]) ? $r->ext_institucion[$idx] : "Institución " . ($idx + 1);
                
                if (!isset($institucionesPorIndex[$idx])) {
                    $institucionesPorIndex[$idx] = [
                        'nombre' => $nombreInstitucion,
                        'jornada_id' => $jornadaId,
                        'horas' => 0
                    ];
                }
            }
            
            // Ahora sumar las horas de cada horario a su institución correspondiente
            foreach ($r->ext_inst_index as $i => $instIndex) {
                if (!isset($institucionesPorIndex[$instIndex])) continue;
                
                $inicio = $r->ext_hora_inicio[$i] ?? null;
                $fin = $r->ext_hora_fin[$i] ?? null;
                
                if (!empty($inicio) && !empty($fin)) {
                    $inicioTime = strtotime($inicio);
                    $finTime = strtotime($fin);
                    $institucionesPorIndex[$instIndex]['horas'] += ($finTime - $inicioTime) / 3600;
                }
            }
            
            // Validar cada institución
            foreach ($institucionesPorIndex as $idx => $instData) {
                $jornadaExterno = \App\Models\Jornada::find($instData['jornada_id']);
                if (!$jornadaExterno) continue;
                
                $horasInstitucion = round($instData['horas'], 1);
                $horasRequeridas = $jornadaExterno->horas_por_semana;
                
                if ($horasInstitucion != $horasRequeridas) {
                    $diferenciaExt = $horasInstitucion - $horasRequeridas;
                    if ($diferenciaExt > 0) {
                        $mensajeExt = "Institución \"" . $instData['nombre'] . "\": EXCEDE la jornada por " . abs($diferenciaExt) . " horas. Asignadas: " . $horasInstitucion . "h | Requeridas: " . $horasRequeridas . "h";
                    } else {
                        $mensajeExt = "Institución \"" . $instData['nombre'] . "\": FALTAN " . abs($diferenciaExt) . " horas para completar la jornada. Asignadas: " . $horasInstitucion . "h | Requeridas: " . $horasRequeridas . "h";
                    }
                    return back()->withInput()->withErrors(['horas_externas' => $mensajeExt]);
                }
            }
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

                // Si son del mismo tipo (ambos UCR o ambos externos): no pueden solaparse
                if ($h1['tipo'] === $h2['tipo']) {
                    if ($h1['inicio'] < $h2['fin'] && $h1['fin'] > $h2['inicio']) {
                        return back()->withInput()->withErrors([
                            'horarios' => "Conflicto detectado en {$h1['dia']}: los horarios {$h1['tipo']} se solapan",
                        ]);
                    }
                }

                // Si son de diferente tipo (UCR vs externo): no pueden solaparse Y debe haber 1h de diferencia
                if ($h1['tipo'] !== $h2['tipo']) {
                    // Primero verificar que no se solapen
                    if ($h1['inicio'] < $h2['fin'] && $h1['fin'] > $h2['inicio']) {
                        return back()->withInput()->withErrors([
                            'horarios' => "Conflicto detectado en {$h1['dia']}: los horarios UCR y externos no pueden solaparse",
                        ]);
                    }
                    
                    // Luego verificar que haya mínimo 1 hora de diferencia
                    // Calcular la distancia entre el fin de uno y el inicio del otro
                    $distancia = 0;
                    if ($h1['fin'] <= $h2['inicio']) {
                        // h1 termina antes que h2 comienza
                        $distancia = $h2['inicio'] - $h1['fin'];
                    } else if ($h2['fin'] <= $h1['inicio']) {
                        // h2 termina antes que h1 comienza
                        $distancia = $h1['inicio'] - $h2['fin'];
                    }
                    
                    if ($distancia < 3600) { // 3600 seg = 1h
                        return back()->withInput()->withErrors([
                            'horarios' => "Debe haber al menos 1 hora de diferencia entre horarios UCR y externos en {$h1['dia']}",
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
                        'horarios' => 'Los horarios de la UCR deben iniciar mínimo a las 7:00 AM',
                    ]);
                }

                if ($finMin > 1260) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'Los horarios de la UCR deben finalizar máximo a las 21:00 (9:00 PM)',
                    ]);
                }

                // Bloque 12:01 - 12:59 (hora de almuerzo)
                // No se puede iniciar ni finalizar en este rango
                if (
                    ($inicioMin > 720 && $inicioMin < 780) ||
                    ($finMin > 720 && $finMin < 780)
                ) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'No se pueden crear horarios entre las 12:01 PM y 12:59 PM (hora de almuerzo)',
                    ]);
                }
                
                // Tampoco puede abarcar el periodo de almuerzo
                if ($inicioMin <= 720 && $finMin >= 780) {
                    return back()->withInput()->withErrors([
                        'horarios' => 'Los horarios no pueden abarcar la hora de almuerzo (12:01-12:59). Divida el horario en dos bloques.',
                    ]);
                }
            }
        }

        // Calcular horas totales de todos los cargos UCR
        $horasTotales = 0;
        if ($r->has('ucr_jornada')) {
            foreach ($r->ucr_jornada as $jornadaId) {
                if (!empty($jornadaId)) {
                    $jornadaUCR = \App\Models\Jornada::find($jornadaId);
                    if ($jornadaUCR) {
                        $horasTotales += $jornadaUCR->horas_por_semana;
                    }
                }
            }
        }

        // Obtener el primer cargo UCR como cargo principal de la declaración
        $cargoPrincipal = null;
        if ($r->has('ucr_cargo')) {
            foreach ($r->ucr_cargo as $cargoId) {
                if (!empty($cargoId)) {
                    $cargoPrincipal = $cargoId;
                    break;
                }
            }
        }

        // Actualizar la declaración
        $usuario = Usuario::find($data['id_usuario']);

        $d->update([
            'id_usuario' => $data['id_usuario'],
            'id_formulario' => $data['id_formulario'],
            'id_unidad' => $data['id_unidad'],
            'id_cargo' => $cargoPrincipal,
            'identificacion' => $usuario->identificacion ?? null,
            'telefono' => $usuario->telefono ?? null,
            'fecha_desde' => $data['fecha_desde'] ?? null,
            'fecha_hasta' => $data['fecha_hasta'] ?? null,
            'horas_totales' => $horasTotales,
            'observaciones_adicionales' => $r->observaciones_adicionales ?? null,
        ]);

        // Eliminar horarios existentes y crear nuevos
        $d->horarios()->delete();

        // Guardar horarios UCR
        if ($r->has('ucr_dia')) {
            foreach ($r->ucr_dia as $i => $dia) {
                if (empty($dia)) continue;
                
                // Obtener el índice del cargo para este horario
                $cargoIndex = isset($r->ucr_cargo_index[$i]) ? $r->ucr_cargo_index[$i] : 0;
                $cargoId = isset($r->ucr_cargo[$cargoIndex]) && !empty($r->ucr_cargo[$cargoIndex]) ? $r->ucr_cargo[$cargoIndex] : null;
                
                // Obtener jornada del cargo
                $jornadaId = isset($r->ucr_jornada[$cargoIndex]) && !empty($r->ucr_jornada[$cargoIndex]) ? $r->ucr_jornada[$cargoIndex] : null;
                
                // Obtener fechas del cargo (fijas para todo el cargo)
                $fechaDesde = isset($r->ucr_cargo_fecha_desde[$cargoIndex]) ? $r->ucr_cargo_fecha_desde[$cargoIndex] : null;
                $fechaHasta = isset($r->ucr_cargo_fecha_hasta[$cargoIndex]) ? $r->ucr_cargo_fecha_hasta[$cargoIndex] : null;
                
                Horario::create([
                    'id_declaracion' => $d->id_declaracion,
                    'id_cargo' => $cargoId,
                    'id_jornada' => $jornadaId,
                    'tipo' => 'ucr',
                    'dia' => $dia,
                    'hora_inicio' => $r->ucr_hora_inicio[$i],
                    'hora_fin' => $r->ucr_hora_fin[$i],
                    'desde' => $fechaDesde,
                    'hasta' => $fechaHasta,
                ]);
            }
        }

        // Guardar horarios externos
        if ($r->has('ext_dia') && $r->has('ext_inst_index')) {
            foreach ($r->ext_dia as $i => $dia) {
                if (empty($dia)) continue;
                
                // Obtener el índice de institución para este horario
                $instIndex = $r->ext_inst_index[$i];
                
                // Obtener el id_jornada, nombre de institución y cargo del índice correspondiente
                $jornadaId = isset($r->ext_jornada[$instIndex]) ? $r->ext_jornada[$instIndex] : null;
                $nombreInstitucion = isset($r->ext_institucion[$instIndex]) ? $r->ext_institucion[$instIndex] : null;
                $cargoExterno = isset($r->ext_cargo[$instIndex]) ? $r->ext_cargo[$instIndex] : null;
                
                // Obtener fechas de la institución (fijas para toda la institución)
                $fechaDesde = isset($r->ext_inst_fecha_desde[$instIndex]) ? $r->ext_inst_fecha_desde[$instIndex] : null;
                $fechaHasta = isset($r->ext_inst_fecha_hasta[$instIndex]) ? $r->ext_inst_fecha_hasta[$instIndex] : null;
                
                Horario::create([
                    'id_declaracion' => $d->id_declaracion,
                    'id_jornada' => $jornadaId,
                    'tipo' => 'externo',
                    'dia' => $dia,
                    'hora_inicio' => $r->ext_hora_inicio[$i],
                    'hora_fin' => $r->ext_hora_fin[$i],
                    'lugar' => $nombreInstitucion,
                    'cargo' => $cargoExterno,
                    'desde' => $fechaDesde,
                    'hasta' => $fechaHasta,
                ]);
            }
        }

        //🔔 Notificación: Sistema completo (BD + Email)
        $notificacionService = new NotificacionService();
        $notificacionService->notificarEditarDeclaracion($d);

        return redirect()
            ->route('declaraciones.show', $d->id_declaracion)
            ->with('ok', 'Declaración actualizada correctamente');
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
        $declaracion = Declaracion::findOrFail($id);

        //🔔 Notificación: Enviar antes de eliminar
        $notificacionService = new NotificacionService();
        $notificacionService->notificarEliminarDeclaracion($declaracion);

        $declaracion->delete();

        return back()->with('ok', 'Declaración eliminada');
    }

    /**
     * API: Obtener unidades académicas por sede
     */
    public function getUnidadesPorSede($id_sede)
    {
        try {
            // Verificar que la sede existe
            $sede = Sede::find($id_sede);
            if (!$sede) {
                return response()->json(['error' => 'Sede no encontrada'], 404);
            }

            // Obtener unidades activas de la sede
            $unidades = UnidadAcademica::where('id_sede', $id_sede)
                ->where('estado', 'ACTIVA')
                ->orderBy('nombre')
                ->get(['id_unidad', 'nombre']);
            
            \Log::info("API: Sede {$id_sede} tiene " . $unidades->count() . " unidades activas");
            
            return response()->json($unidades);
            
        } catch (\Exception $e) {
            \Log::error("Error en getUnidadesPorSede: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    // Helper: HH:mm a minutos
    private function horaAMinutos($hora)
    {
        [$h, $m] = explode(':', $hora);
        return intval($h) * 60 + intval($m);
    }
}
