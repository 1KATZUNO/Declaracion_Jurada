@extends('layout')
@section('titulo', 'Nuevo Cargo')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Registrar Cargo</h2>

  <form method="POST" action="{{ route('cargos.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block font-semibold text-gray-700">Nombre</label>
      <input type="text" name="nombre" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
    </div>
    <div>
      <label class="block font-semibold text-gray-700">Jornada</label>
      <input type="text" name="jornada" placeholder="Ej: 7/8 T.C." class="w-full border rounded-lg p-2 focus:ring-indigo-500">
    </div>
    <div>
      <label class="block font-semibold text-gray-700">Descripción</label>
      <textarea name="descripcion" rows="3" class="w-full border rounded-lg p-2 focus:ring-indigo-500"></textarea>
    </div>
    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('cargos.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Guardar</button>
    </div>
  </form>
</div>
@endsection
