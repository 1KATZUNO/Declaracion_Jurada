<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Declaracion;

class DeclaracionController extends Controller
{
    public function index(Request $req)
    {
        return Declaracion::where('user_id', $req->user()->id)->get();
    }

    public function store(Request $req)
    {
        $req->validate([
            'formulario_id' => 'nullable|integer',
            'data' => 'required|array'
        ]);

        $decl = Declaracion::create([
            'user_id' => $req->user()->id,
            'formulario_id' => $req->formulario_id,
            'data' => $req->data,
            'estado' => 'generada'
        ]);

        return response()->json($decl, 201);
    }

    public function show(Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) {
            return response()->json(['message'=>'No autorizado'], 403);
        }
        return $declaracion;
    }

    public function update(Request $req, Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) {
            return response()->json(['message'=>'No autorizado'], 403);
        }
        $req->validate(['data'=>'required|array']);
        $declaracion->update(['data'=>$req->data]);
        return $declaracion;
    }

    public function destroy(Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) {
            return response()->json(['message'=>'No autorizado'], 403);
        }
        $declaracion->delete();
        return response()->json(['message'=>'Eliminada']);
    }
}
