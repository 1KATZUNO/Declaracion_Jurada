@extends('layout')

@section('titulo', 'Detalles del Documento')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-3xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Detalles del Documento</h2>

  <p><strong>Profesor:</strong> {{ $doc->declaracion->usuario->nombre }} {{ $doc->declaracion->usuario->apellido }}</p>
  <p><strong>Formato:</strong> {{ $doc->formato }}</p>
  <p><strong>Fecha Generación:</strong> {{ $doc->fecha_generacion }}</p>
  <p><strong>Archivo:</strong> <a href="{{ asset($doc->archivo) }}" download="{{ basename($doc->archivo) }}" target="_blank" rel="noopener" class="text-indigo-600 underline">{{ basename($doc->archivo) }}</a></p>

  <div class="mt-6 flex justify-end">
    <x-button href="{{ route('documentos.index') }}" color="indigo">⬅️ Volver</x-button>
  </div>
</div>
@endsection
