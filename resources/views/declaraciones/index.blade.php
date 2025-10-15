@extends('layout')

@section('titulo', 'Declaraciones Juradas')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Declaraciones Juradas</h2>
    <x-button href="{{ route('declaraciones.create') }}" color="blue">➕ Nueva Declaración</x-button>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Profesor</th>
        <th class="py-2 px-3 text-left">Unidad</th>
        <th class="py-2 px-3 text-left">Cargo</th>
        <th class="py-2 px-3 text-left">Desde</th>
        <th class="py-2 px-3 text-left">Hasta</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($declaraciones as $d)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $d->usuario->nombre }} {{ $d->usuario->apellido }}</td>
        <td class="py-2 px-3">{{ $d->unidad->nombre }}</td>
        <td class="py-2 px-3">{{ $d->cargo->nombre }}</td>
        <td class="py-2 px-3">{{ $d->fecha_desde }}</td>
        <td class="py-2 px-3">{{ $d->fecha_hasta }}</td>
        <td class="py-2 px-3 text-center space-x-2">
          <x-button href="{{ route('declaraciones.show', $d->id_declaracion) }}" color="indigo">👁️ Ver</x-button>
          <x-button href="{{ route('declaraciones.exportar', $d->id_declaracion) }}" color="green">📤 Exportar Excel</x-button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
