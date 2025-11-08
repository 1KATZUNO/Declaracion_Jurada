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
        // Asegurar que las jornadas base existan (TC + fracciones)
        $this->ensureConversionesBase();

        $jornadas = Jornada::orderBy('horas_por_semana', 'asc')->paginate(10);
        // pasar la jornada TC para usar el botón "Modificar jornada TC"
        $tcJornada = Jornada::where('tipo','TC')->first();
        return view('jornadas.index', compact('jornadas','tcJornada'));
    }
 
    /**
     * Asegura que existan las jornadas base (TC, 3/4, 1/2, 1/4, 1/8).
     * Si faltan, se crean usando el valor TC (por defecto 40h).
     */
    protected function ensureConversionesBase()
    {
        $tc = Jornada::where('tipo','TC')->first();
        if (!$tc) {
            // crear TC con valores mínimos
            $tc = Jornada::create(['tipo' => 'TC', 'horas_por_semana' => 40]);
        }

        // NUEVO: mapa completo de fracciones (ordenado ascendente)
        $map = [
            '1/8' => 0.125,
            '1/4' => 0.25,
            '3/8' => 0.375,
            '1/2' => 0.5,
            '5/8' => 0.625,
            '3/4' => 0.75,
            '7/8' => 0.875,
            // TC se considera la base (1.0)
        ];

        foreach ($map as $tipo => $factor) {
            $j = Jornada::where('tipo', $tipo)->first();
            $nuevo = (int) round($tc->horas_por_semana * $factor);
            if (!$j) {
                Jornada::create(['tipo' => $tipo, 'horas_por_semana' => $nuevo]);
            } else {
                // mantener existente (propagación se hace al editar TC)
            }
        }
    }

    public function create()
    {
        $jornada = new Jornada();
        // obtener horas de referencia (TC) o 40 por defecto
        $tcHoras = Jornada::where('tipo','TC')->value('horas_por_semana') ?? 40;
        return view('jornadas.form', [
            'jornada' => $jornada,
            'mode' => 'create',
            'tcHoras' => $tcHoras, // <-- añadido
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

        $j = Jornada::create($data);

        // Si se creó/actualizó la jornada TC, propagar a jornadas fraccionarias existentes
        if ($data['tipo'] === 'TC') {
            $this->propagarDesdeTC(intval($data['horas_por_semana']));
        }

        return redirect()->route('jornadas.index')
            ->with('success', 'Jornada creada correctamente.');
    }

    public function edit($id)
    {
        $jornada = Jornada::findOrFail($id);
        // Solo permitir editar la jornada TC desde esta UI
        if ($jornada->tipo !== 'TC') {
            return redirect()->route('jornadas.index')
                ->withErrors(['jornada' => 'No está permitido editar jornadas automáticas desde esta interfaz.']);
        }
        $tcHoras = Jornada::where('tipo','TC')->value('horas_por_semana') ?? 40;
        return view('jornadas.form', [
            'jornada' => $jornada,
            'mode' => 'edit',
            'tcHoras' => $tcHoras,
        ]);
    }

    public function update(Request $request, $id)
    {
        $jornada = Jornada::findOrFail($id);

        // Solo permitir updates sobre la jornada TC
        if ($jornada->tipo !== 'TC') {
            return redirect()->route('jornadas.index')
                ->withErrors(['jornada' => 'No está permitido modificar jornadas automáticas desde esta interfaz.']);
        }

        $data = $request->validate([
            'tipo' => 'required|string|max:20|unique:jornada,tipo,' . $jornada->id_jornada . ',id_jornada',
            'horas_por_semana' => 'required|integer|min:1|max:168',
        ]);

        // actualizar TC y propagar
        $jornada->update($data);
        $this->setTCBase(intval($data['horas_por_semana']));

        return redirect()->route('jornadas.index')
            ->with('success', 'Jornada (TC) actualizada y conversiones propagadas.');
    }

    public function destroy($id)
    {
        // Protección: no permitir eliminar jornadas desde esta interfaz.
        // Si realmente se desea eliminar, hacerlo mediante una tarea administrativa.
        return back()->withErrors(['jornada' => 'No está permitido eliminar jornadas desde esta interfaz.']);
    }

    /**
     * Establece/actualiza la jornada TC con el valor dado y propaga las fracciones.
     */
    protected function setTCBase(int $tcHoras)
    {
        $tc = Jornada::where('tipo', 'TC')->first();
        if (!$tc) {
            $tc = Jornada::create(['tipo' => 'TC', 'horas_por_semana' => $tcHoras]);
        } else {
            $tc->update(['horas_por_semana' => $tcHoras]);
        }

        // Propagar a fracciones existentes
        $this->propagarDesdeTC($tcHoras);
    }

    /**
     * Propaga los valores fraccionarios a las jornadas existentes según la jornada TC.
     * Si no existen las jornadas fraccionarias en la BD, no las crea (solo actualiza las existentes).
     */
    protected function propagarDesdeTC(int $tcHoras)
    {
        // PROPAGACIÓN hacia todas las fracciones y TC si procede
        $map = [
            '1/8' => 0.125,
            '1/4' => 0.25,
            '3/8' => 0.375,
            '1/2' => 0.5,
            '5/8' => 0.625,
            '3/4' => 0.75,
            '7/8' => 0.875,
            // TC no se actualiza aquí porque es la base
        ];

        foreach ($map as $tipo => $factor) {
            $j = Jornada::where('tipo', $tipo)->first();
            if ($j) {
                $nuevo = (int) round($tcHoras * $factor);
                $j->update(['horas_por_semana' => $nuevo]);
            }
        }
    }
}
