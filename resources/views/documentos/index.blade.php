@extends('layout')

@section('titulo', 'Documentos Generados')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Documentos de Declaraciones</h2>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Profesor</th>
        <th class="py-2 px-3 text-left">Archivo</th>
        <th class="py-2 px-3 text-left">Formato</th>
        <th class="py-2 px-3 text-left">Fecha Generación</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($documentos as $doc)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $doc->declaracion->usuario->nombre }} {{ $doc->declaracion->usuario->apellido }}</td>
        <td class="py-2 px-3">{{ basename($doc->archivo) }}</td>
        <td class="py-2 px-3">{{ $doc->formato }}</td>
        <td class="py-2 px-3">{{ $doc->fecha_generacion }}</td>
        <td class="py-2 px-3 text-center space-x-2">
          <a href="{{ asset($doc->archivo) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg font-semibold">⬇️ Descargar</a>
          <form action="{{ route('documentos.destroy', $doc->id_documento) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold"
              onclick="return confirm('¿Eliminar este documento?')">🗑️ Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
