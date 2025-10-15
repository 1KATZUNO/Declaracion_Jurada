@extends('layout')
@section('titulo', 'Nueva Declaración Jurada')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-4xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Registrar Nueva Declaración Jurada</h2>

  <form method="POST" action="{{ route('declaraciones.store') }}" class="space-y-6">
    @csrf
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold text-gray-700">Usuario</label>
        <select name="id_usuario" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
          <option value="">Seleccione...</option>
          @foreach($usuarios as $u)
            <option value="{{ $u->id_usuario }}">{{ $u->nombre }} {{ $u->apellido }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-semibold text-gray-700">Unidad Académica</label>
        <select name="id_unidad" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
          @foreach($unidades as $u)
            <option value="{{ $u->id_unidad }}">{{ $u->nombre }} — {{ $u->sede->nombre }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-semibold text-gray-700">Cargo</label>
        <select name="id_cargo" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
          @foreach($cargos as $c)
            <option value="{{ $c->id_cargo }}">{{ $c->nombre }} ({{ $c->jornada }})</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-semibold text-gray-700">Formulario Base</label>
        <select name="id_formulario" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
          @foreach($formularios as $f)
            <option value="{{ $f->id_formulario }}">{{ $f->titulo }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-semibold text-gray-700">Fecha Desde</label>
        <input type="date" name="fecha_desde" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
      </div>

      <div>
        <label class="block font-semibold text-gray-700">Fecha Hasta</label>
        <input type="date" name="fecha_hasta" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
      </div>

      <div>
        <label class="block font-semibold text-gray-700">Horas Totales</label>
        <input type="number" step="0.5" name="horas_totales" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
      </div>
    </div>

    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('declaraciones.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Guardar</button>
    </div>
  </form>
</div>
@endsection
