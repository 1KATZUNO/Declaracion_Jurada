<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Jornada;
use App\Models\HorarioDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //  se usa DB para leer la tabla 'declaracion'

class HorarioController extends Controller
{
    // Muestra todos los horarios
    public function index()
    {
        // Cargar detalles y jornada para poder mostrar todos los intervalos del horario
        $horarios = Horario::with(['detalles','jornada'])->orderBy('id_horario', 'desc')->paginate(10);
        return view('horarios.index', compact('horarios'));
    }

    // EL formulario de creación
    public function create(Request $request)
    {
        // Ya no pedimos declaraciones aquí: los horarios son plantillas independientes (tipo UCR)
        $jornadas = Jornada::orderBy('tipo')->get();
        return view('horarios.create', compact('jornadas'));
    }

    // Guardar un nuevo horario (ahora crea 1 registro padre + N detalles)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jornada'     => 'required|exists:jornada,id_jornada',
            'dia.*'          => 'required|string',
            'hora_inicio.*'  => 'required|date_format:H:i',
            'hora_fin.*'     => 'required|date_format:H:i|after:hora_inicio.*',
        ]);

        $jornada = Jornada::findOrFail($validated['id_jornada']);
        $targetMinutes = intval($jornada->horas_por_semana) * 60;

        $dias = $request->input('dia', []);
        $horasInicio = $request->input('hora_inicio', []);
        $horasFin = $request->input('hora_fin', []);

        // Construir intervalos y validar solapamientos en servidor
        $intervalsByDay = [];
        $totalMinutes = 0;

        $count = max(count($dias), count($horasInicio), count($horasFin));
        for ($i = 0; $i < $count; $i++) {
            $dia = $dias[$i] ?? null;
            $hi = $horasInicio[$i] ?? null;
            $hf = $horasFin[$i] ?? null;

            if (!$dia || !$hi || !$hf) {
                return back()->withInput()->withErrors(['horarios' => 'Cada fila debe tener día, hora inicio y hora fin.']);
            }

            $start = strtotime($hi);
            $end   = strtotime($hf);
            if ($end <= $start) {
                return back()->withInput()->withErrors(['horarios' => "Hora fin debe ser mayor que hora inicio para el día {$dia}."]);
            }

            $minutes = intval(($end - $start) / 60);
            $totalMinutes += $minutes;

            // conservar las horas originales para persistir como detalle
            $intervalsByDay[$dia][] = ['start' => $start, 'end' => $end, 'hora_inicio' => $hi, 'hora_fin' => $hf];
        }

        // Verificar solapamientos por día
        foreach ($intervalsByDay as $dia => $intervals) {
            usort($intervals, function($a,$b){ return $a['start'] <=> $b['start']; });
            $prevEnd = null;
            foreach ($intervals as $int) {
                if ($prevEnd !== null && $int['start'] < $prevEnd) {
                    return back()->withInput()->withErrors(['horarios' => "Solapamiento detectado en $dia (no está permitido)."]);
                }
                $prevEnd = $int['end'];
            }
        }

        // Comparar total con jornada
        if ($totalMinutes !== $targetMinutes) {
            $humanTotal = round($totalMinutes / 60, 2);
            $humanTarget = round($targetMinutes / 60, 2);
            return back()->withInput()->withErrors(['horarios' => "Total de horas ({$humanTotal}h) debe ser igual a las horas de la jornada ({$humanTarget}h)."]);
        }

        // Determinar primera fila para poblar columnas requeridas en la tabla horario
        $firstDia = null;
        $firstHi = null;
        $firstHf = null;
        foreach ($intervalsByDay as $d => $ints) {
            if (!empty($ints)) {
                $firstDia = $d;
                $firstHi = $ints[0]['hora_inicio'];
                $firstHf = $ints[0]['hora_fin'];
                break;
            }
        }

        // Crear registro padre (plantilla) en tabla horario (sin id_declaracion)
        // Rellenamos dia/hora_inicio/hora_fin con la primera fila para respetar esquema NOT NULL
        $horario = Horario::create([
            'id_jornada'  => $validated['id_jornada'],
            'tipo'        => 'ucr',
            'dia'         => $firstDia ?? $dias[0] ?? 'Lunes',
            'hora_inicio' => $firstHi ?? $horasInicio[0] ?? '00:00',
            'hora_fin'    => $firstHf ?? $horasFin[0] ?? '00:00',
            'lugar'       => null,
        ]);

        // Persistir detalles
        foreach ($intervalsByDay as $dia => $intervals) {
            foreach ($intervals as $int) {
                HorarioDetalle::create([
                    'id_horario'  => $horario->id_horario,
                    'dia'         => $dia,
                    'hora_inicio' => $int['hora_inicio'],
                    'hora_fin'    => $int['hora_fin'],
                ]);
            }
        }

        return redirect()->route('horarios.index')
                         ->with('success','Horario plantilla registrado correctamente (registro padre + detalles).');
    }

    // Eliminar horario
    public function destroy($id)
    {
        $horario = Horario::findOrFail($id);
        $horario->delete();

        return redirect()->route('horarios.index')
                         ->with('success', 'Horario eliminado correctamente');
    }
    // PUT/PATCH /horarios/{id}
public function update(Request $request, $id)
{
    $horario = Horario::findOrFail($id);

    // Validación (mismos criterios de creación + campos opcionales del modelo)
    $validated = $request->validate([
        'tipo'         => 'required|in:ucr,externo',
        'dia'          => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
        'hora_inicio'  => 'required|date_format:H:i',
        'hora_fin'     => 'required|date_format:H:i|after:hora_inicio',
        'lugar'        => 'nullable|string|max:255',
        'cargo'        => 'nullable|string|max:255',
        'jornada'      => 'nullable|string|max:50',
        'desde'        => 'nullable|date',
        'hasta'        => 'nullable|date|after_or_equal:desde',
    ], [
        'hora_fin.after'           => 'La hora fin debe ser mayor que la hora inicio.',
        'hasta.after_or_equal'     => 'La fecha "hasta" debe ser igual o posterior a "desde".',
    ]);

    // Actualiza solo lo validado
    $horario->update($validated);

    // Volvemos a la lista con mensaje
    return back()->with('success', 'Horario actualizado correctamente.');
}

}
