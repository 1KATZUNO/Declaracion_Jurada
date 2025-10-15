@extends('layout')

@section('titulo', 'Sedes')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Sedes Universitarias</h2>
    <x-button href="{{ route('sedes.create') }}" color="blue">➕ Nueva Sede</x-button>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Nombre</th>
        <th class="py-2 px-3 text-left">Ubicación</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($sedes as $s)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $s->nombre }}</td>
        <td class="py-2 px-3">{{ $s->ubicacion ?? '—' }}</td>
        <td class="py-2 px-3 text-center space-x-2">
          <x-button href="{{ route('sedes.edit', $s->id_sede) }}" color="indigo">✏️ Editar</x-button>
          <form action="{{ route('sedes.destroy', $s->id_sede) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold"
              onclick="return confirm('¿Eliminar esta sede?')">🗑️ Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
