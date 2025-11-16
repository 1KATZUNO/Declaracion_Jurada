<?php

namespace App\Http\Controllers;

use App\Models\ActividadLog;
use Illuminate\Http\Request;

class ActividadLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActividadLog::with('usuario')
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('usuario')) {
            $query->where('id_usuario', $request->usuario);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $logs = $query->paginate(50);

        // Obtener datos para filtros
        $acciones = ActividadLog::distinct()->pluck('accion');
        $modulos = ActividadLog::distinct()->pluck('modulo');

        return view('actividad-logs.index', compact('logs', 'acciones', 'modulos'));
    }

    public function show($id)
    {
        $log = ActividadLog::with('usuario')->findOrFail($id);
        return view('actividad-logs.show', compact('log'));
    }

    public function destroy($id)
    {
        $log = ActividadLog::findOrFail($id);
        $log->delete();

        return redirect()->route('actividad-logs.index')
            ->with('success', 'Log eliminado correctamente');
    }

    public function limpiar(Request $request)
    {
        $diasAntiguedad = $request->input('dias', 90);
        
        $deleted = ActividadLog::where('created_at', '<', now()->subDays($diasAntiguedad))->delete();

        return redirect()->route('actividad-logs.index')
            ->with('success', "Se eliminaron {$deleted} registros de más de {$diasAntiguedad} días");
    }
}

