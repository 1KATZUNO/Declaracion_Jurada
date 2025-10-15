@extends('layout')
@section('titulo', 'Nueva Unidad Académica')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Registrar Unidad Académica</h2>

  <form method="POST" action="{{ route('unidades.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block font-semibold text-gray-700">Nombre</label>
      <input type="text" name="nombre" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
    </div>

    <div>
      <label class="block font-semibold text-gray-700">Sede Asociada</label>
      <select name="id_sede" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
        <option value="">Seleccione una sede...</option>
        @foreach($sedes as $s)
          <option value="{{ $s->id_sede }}">{{ $s->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('unidades.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Guardar</button>
    </div>
  </form>
</div>
@endsection
