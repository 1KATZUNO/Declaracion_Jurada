@extends('layout')
@section('titulo', 'Editar Formulario')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Editar Formulario</h2>

  <form method="POST" action="{{ route('formularios.update', $formulario->id_formulario) }}" class="space-y-4">
    @csrf @method('PUT')
    <div>
      <label class="block font-semibold text-gray-700">Título</label>
      <input type="text" name="titulo" value="{{ $formulario->titulo }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
    </div>
    <div>
      <label class="block font-semibold text-gray-700">Descripción</label>
      <textarea name="descripcion" rows="3" class="w-full border rounded-lg p-2 focus:ring-indigo-500">{{ $formulario->descripcion }}</textarea>
    </div>
    <div>
      <label class="block font-semibold text-gray-700">Fecha de Creación</label>
      <input type="date" name="fecha_creacion" value="{{ $formulario->fecha_creacion }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
    </div>
    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('formularios.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Actualizar</button>
    </div>
  </form>
</div>
@endsection
