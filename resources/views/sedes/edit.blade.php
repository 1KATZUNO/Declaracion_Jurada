@extends('layout')
@section('titulo', 'Editar Sede')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Editar Sede</h2>

  <form method="POST" action="{{ route('sedes.update', $sede->id_sede) }}" class="space-y-4">
    @csrf @method('PUT')
    <div>
      <label class="block font-semibold text-gray-700">Nombre</label>
      <input type="text" name="nombre" value="{{ $sede->nombre }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
    </div>
    <div>
      <label class="block font-semibold text-gray-700">Ubicación</label>
      <input type="text" name="ubicacion" value="{{ $sede->ubicacion }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500">
    </div>
    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('sedes.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Actualizar</button>
    </div>
  </form>
</div>
@endsection
