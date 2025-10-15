@extends('layout')

@section('titulo', 'Formularios')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Formularios Disponibles</h2>
    <x-button href="{{ route('formularios.create') }}" color="blue">➕ Nuevo Formulario</x-button>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Título</th>
        <th class="py-2 px-3 text-left">Descripción</th>
        <th class="py-2 px-3 text-left">Fecha de Creación</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($formularios as $f)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $f->titulo }}</td>
        <td class="py-2 px-3 text-gray-700 text-sm">{{ Str::limit($f->descripcion, 80) }}</td>
        <td class="py-2 px-3">{{ $f->fecha_creacion }}</td>
        <td class="py-2 px-3 text-center space-x-2">
          <x-button href="{{ route('formularios.edit', $f->id_formulario) }}" color="indigo">✏️ Editar</x-button>
          <form action="{{ route('formularios.destroy', $f->id_formulario) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold"
              onclick="return confirm('¿Eliminar este formulario?')">🗑️ Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
