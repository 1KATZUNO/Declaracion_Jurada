@extends('layout')
 @csrf
@section('titulo', 'Detalles del Documento')

@section('contenido')
<div class="container mx-auto w-full max-w-3xl px-2 sm:px-4 md:px-8 py-8 bg-white shadow-lg rounded-xl">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Detalles del Documento</h2>

  <p><strong>Profesor:</strong> {{ $doc->declaracion->usuario->nombre }} {{ $doc->declaracion->usuario->apellido }}</p>
  <p><strong>Formato:</strong> {{ $doc->formato }}</p>
  <p><strong>Fecha Generación:</strong> {{ $doc->fecha_generacion }}</p>
  <p><strong>Archivo:</strong>
    @php
      $fileUrl = (strpos($doc->archivo, 'public/') === 0)
        ? \Illuminate\Support\Facades\Storage::url($doc->archivo)
        : asset($doc->archivo);
    @endphp
    <a href="{{ $fileUrl }}" download="{{ basename($doc->archivo) }}" target="_blank" rel="noopener" class="text-indigo-600 underline">{{ basename($doc->archivo) }}</a>
  </p>

  <div class="mt-6 flex flex-col md:flex-row justify-end gap-3">
    <x-button href="{{ route('documentos.index') }}" color="indigo">⬅️ Volver</x-button>
  </div>
</div>
@endsection
