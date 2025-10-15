<?php

namespace App\Http\Controllers;

use App\Models\{Documento,Declaracion};

class DocumentoController extends Controller
{
    public function index(){
        $documentos = Documento::with('declaracion.usuario')->latest('fecha_generacion')->get();
        return view('documentos.index', compact('documentos'));
    }

    public function show($id){
        $doc = Documento::with('declaracion.usuario')->findOrFail($id);
        return view('documentos.show', compact('doc'));
    }

    public function destroy($id){
        Documento::findOrFail($id)->delete();
        return back()->with('ok','Documento eliminado');
    }
}


