@extends('layout')
@section('titulo', 'Editar Cargo')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Editar Cargo</h2>

  <form method="POST" action="{{ route('cargos.update', $cargo->id_cargo) }}" class="space-y-4">
    @csrf @method('PUT')
    <div>
      <label class="block font-semibold text-gray-700">Nombre</label>
      <input type="text" name="nombre" value="{{ $cargo->nombre }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
    </div>
    <div>
      <label class="block font-semibold text-gray-700">Jornada</label>
      <input type="text" name="jornada" value="{{ $cargo->jornada }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500">
    </div>
    <div>
      <label class="block font-semibold text-gray-700">Descripción</label>
      <textarea name="descripcion" rows="3" class="w-full border rounded-lg p-2 focus:ring-indigo-500">{{ $cargo->descripcion }}</textarea>
    </div>
    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('cargos.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Actualizar</button>
    </div>
  </form>
</div>
@endsection
