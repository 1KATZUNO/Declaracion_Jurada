@extends('layout')

@section('titulo', 'Notificaciones')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Notificaciones del Sistema</h2>
    <x-button href="{{ route('notificaciones.create') }}" color="blue">➕ Nueva Notificación</x-button>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Usuario</th>
        <th class="py-2 px-3 text-left">Mensaje</th>
        <th class="py-2 px-3 text-left">Fecha Envío</th>
        <th class="py-2 px-3 text-left">Estado</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($notificaciones as $n)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $n->usuario->nombre }} {{ $n->usuario->apellido }}</td>
        <td class="py-2 px-3 text-gray-700">{{ Str::limit($n->mensaje, 80) }}</td>
        <td class="py-2 px-3">{{ $n->fecha_envio }}</td>
        <td class="py-2 px-3 capitalize">
          @php
            $color = match($n->estado) {
              'pendiente' => 'bg-yellow-200 text-yellow-900',
              'enviada' => 'bg-blue-200 text-blue-900',
              'leída' => 'bg-green-200 text-green-900',
              default => 'bg-gray-200 text-gray-700'
            };
          @endphp
          <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $color }}">{{ $n->estado }}</span>
        </td>
        <td class="py-2 px-3 text-center">
          <form action="{{ route('notificaciones.destroy', $n->id_notificacion) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold"
              onclick="return confirm('¿Eliminar esta notificación?')">🗑️ Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
