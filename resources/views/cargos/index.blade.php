@extends('layout')

@section('titulo', 'Cargos')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Cargos del Personal</h2>
    <x-button href="{{ route('cargos.create') }}" color="blue">➕ Nuevo Cargo</x-button>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Nombre</th>
        <th class="py-2 px-3 text-left">Jornada</th>
        <th class="py-2 px-3 text-left">Descripción</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($cargos as $c)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $c->nombre }}</td>
        <td class="py-2 px-3">{{ $c->jornada ?? '—' }}</td>
        <td class="py-2 px-3 text-sm text-gray-700">{{ Str::limit($c->descripcion, 60) }}</td>
        <td class="py-2 px-3 text-center space-x-2">
          <x-button href="{{ route('cargos.edit', $c->id_cargo) }}" color="indigo">✏️ Editar</x-button>
          <form action="{{ route('cargos.destroy', $c->id_cargo) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold"
              onclick="return confirm('¿Eliminar este cargo?')">🗑️ Eliminar</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
