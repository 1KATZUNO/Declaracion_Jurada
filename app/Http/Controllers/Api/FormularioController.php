<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formulario;

class FormularioController extends Controller
{
    public function index()
    {
        return Formulario::all();
    }

    public function store(Request $req)
    {
        $req->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'campos' => 'required|array'
        ]);

        $form = Formulario::create($req->all());
        return response()->json($form, 201);
    }

    public function show(Formulario $formulario)
    {
        return $formulario;
    }

    public function update(Request $req, Formulario $formulario)
    {
        $req->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'campos' => 'required|array'
        ]);

        $formulario->update($req->all());
        return $formulario;
    }

    public function destroy(Formulario $formulario)
    {
        $formulario->delete();
        return response()->json(['message'=>'Formulario eliminado']);
    }
}
