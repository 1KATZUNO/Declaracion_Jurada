<?php

namespace App\Http\Controllers;

use App\Models\{Documento, Declaracion, User};
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        // Obtener usuario desde la sesión
        $userId = session('usuario_id');
        $userRol = session('usuario_rol');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión');
        }

        $user = User::find($userId);

        // DEBUG: Log para verificar el usuario
        \Log::info('Usuario autenticado:', [
            'id_usuario' => $user->id_usuario ?? 'NULL',
            'nombre' => $user->nombre ?? 'NULL',
            'rol' => $user->rol ?? 'NULL'
        ]);

        // Consulta base con relaciones
        $query = Documento::with(['declaracion.usuario'])
            ->orderBy('fecha_generacion', 'desc');

        // Si es funcionario, solo puede ver sus propios documentos
        if ($user && $user->rol === 'funcionario') {
            \Log::info('FILTRO APLICADO: Usuario funcionario id_usuario = ' . $user->id_usuario);
            $query->whereHas('declaracion', function ($q) use ($user) {
                $q->where('id_usuario', $user->id_usuario);
            });
        } else {
            \Log::info('SIN FILTRO: Usuario es admin o no autenticado');
        }

        // Filtro por nombre de usuario (solo admin)
        if ($request->filled('buscar') && $user->rol !== 'funcionario') {
            $buscar = $request->buscar;
            $query->whereHas('declaracion.usuario', function ($q) use ($buscar) {
                $q->where('nombre', 'like', '%' . $buscar . '%');
            });
        }

        // Filtro por fecha (disponible para todos)
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_generacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_generacion', '<=', $request->fecha_hasta);
        }

        // Paginación con query strings
        $documentos = $query->paginate(10)->withQueryString();

        return view('documentos.index', compact('documentos', 'user'));
    }

    public function show($id)
    {
        // Obtener usuario desde la sesión
        $userId = session('usuario_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión');
        }
        $user = User::find($userId);

        // Cargar documento con todas las relaciones de la declaración
        $doc = Documento::with([
            'declaracion.usuario',
            'declaracion.formulario',
            'declaracion.unidad.sede',
            'declaracion.cargo',
            'declaracion.horarios'
        ])->findOrFail($id);

        // Si es funcionario, validar que le pertenece
        if ($user && $user->rol === 'funcionario') {
            if (! $doc->declaracion || $doc->declaracion->id_usuario != $user->id_usuario) {
                abort(403, 'No autorizado para ver este documento.');
            }
        }

        return view('documentos.show', compact('doc'));
    }

    public function destroy($id)
    {
        // Obtener usuario desde la sesión
        $userId = session('usuario_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión');
        }
        $user = User::find($userId);

        $doc = Documento::with('declaracion')->findOrFail($id);

        // Validación para funcionarios
        if ($user && $user->rol === 'funcionario') {
            if (! $doc->declaracion || $doc->declaracion->id_usuario != $user->id_usuario) {
                abort(403, 'No autorizado para eliminar este documento.');
            }
        }

        $doc->delete();

        return back()->with('ok', 'Documento eliminado correctamente.');
    }

    public function download($id)
    {
        // Obtener usuario desde la sesión
        $userId = session('usuario_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión');
        }
        $user = User::find($userId);

        // Cargar documento con relaciones
        $doc = Documento::with('declaracion.usuario')->findOrFail($id);

        // Si es funcionario, validar que le pertenece
        if ($user && $user->rol === 'funcionario') {
            if (! $doc->declaracion || $doc->declaracion->id_usuario != $user->id_usuario) {
                abort(403, 'No autorizado para descargar este documento.');
            }
        }

        // Construir ruta completa del archivo
        $archivo = $doc->archivo;
        
        // Si la ruta comienza con "public/", quitar ese prefijo
        if (strpos($archivo, 'public/') === 0) {
            $archivo = str_replace('public/', '', $archivo);
        }
        
        $rutaCompleta = storage_path('app/public/' . $archivo);

        // Verificar que el archivo existe
        if (!file_exists($rutaCompleta)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        // Descargar el archivo
        return response()->download($rutaCompleta, basename($archivo));
    }
}
