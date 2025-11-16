<?php

namespace App\Http\Controllers;

use App\Models\{Documento, Declaracion};
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Consulta base con relaciones
        $query = Documento::with(['declaracion.usuario'])
            ->orderBy('fecha_generacion', 'desc');

        // Si es profesor, solo puede ver sus propios documentos
        if ($user && $user->rol === 'profe') {
            $query->whereHas('declaracion', function ($q) use ($user) {
                $q->where('id_usuario', $user->id);
            });
        }

        // Filtro por nombre de usuario
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('declaracion.usuario', function ($q) use ($buscar) {
                $q->where('nombre', 'like', '%' . $buscar . '%');
            });
        }

        // Paginación con query strings
        $documentos = $query->paginate(10)->withQueryString();

        return view('documentos.index', compact('documentos'));
    }

    public function show($id)
    {
        $user = auth()->user();

        // Cargar documento con relaciones
        $doc = Documento::with('declaracion.usuario')->findOrFail($id);

        // Si es profesor, validar que le pertenece
        if ($user && $user->rol === 'profe') {
            if (! $doc->declaracion || $doc->declaracion->id_usuario != $user->id) {
                abort(403, 'No autorizado para ver este documento.');
            }
        }

        return view('documentos.show', compact('doc'));
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $doc = Documento::with('declaracion')->findOrFail($id);

        // Validación para profesores
        if ($user && $user->rol === 'profe') {
            if (! $doc->declaracion || $doc->declaracion->id_usuario != $user->id) {
                abort(403, 'No autorizado para eliminar este documento.');
            }
        }

        $doc->delete();

        return back()->with('ok', 'Documento eliminado correctamente.');
    }
}
