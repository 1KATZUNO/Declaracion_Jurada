@extends('layout')
@section('titulo', 'Editar Unidad Académica')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Editar Unidad Académica</h2>

  <form method="POST" action="{{ route('unidades.update', $unidad->id_unidad) }}" class="space-y-4">
    @csrf @method('PUT')

    <div>
      <label class="block font-semibold text-gray-700">Nombre</label>
      <input type="text" name="nombre" value="{{ $unidad->nombre }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
    </div>

    <div>
      <label class="block font-semibold text-gray-700">Sede Asociada</label>
      <select name="id_sede" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
        @foreach($sedes as $s)
          <option value="{{ $s->id_sede }}" {{ $unidad->id_sede == $s->id_sede ? 'selected' : '' }}>{{ $s->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('unidades.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Actualizar</button>
    </div>
  </form>
</div>
@endsection
