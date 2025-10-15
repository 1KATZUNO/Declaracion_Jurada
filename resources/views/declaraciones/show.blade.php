@extends('layout')
@section('titulo', 'Detalles de Declaración Jurada')
@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-5xl mx-auto">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold text-indigo-700">Declaración de {{ $d->usuario->nombre }} {{ $d->usuario->apellido }}</h2>
    <x-button href="{{ route('declaraciones.exportar', $d->id_declaracion) }}" color="green">📤 Exportar Excel</x-button>
  </div>

  <div class="grid grid-cols-2 gap-6 mb-6">
    <div>
      <p><span class="font-semibold text-gray-700">Correo:</span> {{ $d->usuario->correo }}</p>
      <p><span class="font-semibold text-gray-700">Teléfono:</span> {{ $d->usuario->telefono ?? '—' }}</p>
      <p><span class="font-semibold text-gray-700">Unidad Académica:</span> {{ $d->unidad->nombre }}</p>
      <p><span class="font-semibold text-gray-700">Sede:</span> {{ $d->unidad->sede->nombre ?? '—' }}</p>
    </div>
    <div>
      <p><span class="font-semibold text-gray-700">Cargo:</span> {{ $d->cargo->nombre }}</p>
      <p><span class="font-semibold text-gray-700">Jornada:</span> {{ $d->cargo->jornada }}</p>
      <p><span class="font-semibold text-gray-700">Desde:</span> {{ $d->fecha_desde }}</p>
      <p><span class="font-semibold text-gray-700">Hasta:</span> {{ $d->fecha_hasta }}</p>
      <p><span class="font-semibold text-gray-700">Horas Totales:</span> {{ $d->horas_totales }}</p>
    </div>
  </div>

  <h3 class="text-lg font-semibold text-indigo-600 mb-2">🕒 Horario Declarado</h3>
  <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 text-left">Día</th>
        <th class="p-2 text-left">Hora Inicio</th>
        <th class="p-2 text-left">Hora Fin</th>
      </tr>
    </thead>
    <tbody>
      @forelse($d->horarios as $h)
      <tr class="odd:bg-gray-50">
        <td class="p-2">{{ $h->dia }}</td>
        <td class="p-2">{{ $h->hora_inicio }}</td>
        <td class="p-2">{{ $h->hora_fin }}</td>
      </tr>
      @empty
      <tr><td colspan="3" class="text-center text-gray-500 p-3">Sin horarios registrados</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="flex justify-end mt-6">
    <x-button href="{{ route('declaraciones.index') }}" color="indigo">⬅️ Volver</x-button>
  </div>
</div>
@endsection
