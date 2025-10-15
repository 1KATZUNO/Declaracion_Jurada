@extends('layout')

@section('titulo', 'Unidades Académicas')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Unidades Académicas</h2>
    <x-button href="{{ route('unidades.create') }}" color="blue">➕ Nueva Unidad</x-button>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Nombre</th>
        <th class="py-2 px-3 text-left">Sede</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($unidades as $u)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $u->nombre }}</td>
        <td class="py-2 px-3">{{ $u->sede->nombre ?? '—' }}</td>
        <td class="py-2 px-3 text-center space-x-2">
          <x-button href="{{ route('unidades.edit', $u->id_unidad) }}" color="indigo">✏️ Editar</x-button>
          <form action="{{ route('unidades.destroy', $u->id_unidad) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold"
              onclick="return confirm('¿Eliminar esta unidad académica?')">🗑️ Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
