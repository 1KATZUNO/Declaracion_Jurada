@extends('layout')
@section('titulo', 'Nueva Notificación')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Crear Notificación</h2>

  <form method="POST" action="{{ route('notificaciones.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block font-semibold text-gray-700">Usuario</label>
      <select name="id_usuario" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
        @foreach($usuarios as $u)
          <option value="{{ $u->id_usuario }}">{{ $u->nombre }} {{ $u->apellido }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block font-semibold text-gray-700">Mensaje</label>
      <textarea name="mensaje" rows="3" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required></textarea>
    </div>

    <div>
      <label class="block font-semibold text-gray-700">Estado</label>
      <select name="estado" class="w-full border rounded-lg p-2 focus:ring-indigo-500" required>
        <option value="pendiente">Pendiente</option>
        <option value="enviada">Enviada</option>
        <option value="leída">Leída</option>
      </select>
    </div>

    <div class="flex justify-end gap-2 pt-4">
      <x-button href="{{ route('notificaciones.index') }}" color="red">Cancelar</x-button>
      <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Guardar</button>
    </div>
  </form>
</div>
@endsection
