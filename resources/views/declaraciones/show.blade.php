@extends('layout')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Detalle de la Declaración Jurada</h2>

    <div class="space-y-2">
        <p><strong>Nombre:</strong> {{ $declaracion->usuario->nombre }} {{ $declaracion->usuario->apellido }}</p>
        <p><strong>Identificación:</strong> {{ $declaracion->usuario->identificacion }}</p>
        <p><strong>Unidad:</strong> {{ $declaracion->unidad->nombre }}</p>
        <p><strong>Cargo:</strong> {{ $declaracion->cargo->nombre }}</p>
        <p><strong>Jornada:</strong> {{ $declaracion->cargo->jornada }}</p>
        <p><strong>Desde:</strong> {{ $declaracion->fecha_desde }} — <strong>Hasta:</strong> {{ $declaracion->fecha_hasta }}</p>
        <p><strong>Horas totales:</strong> {{ $declaracion->horas_totales }}</p>
    </div>

    <hr class="my-6">

    <h3 class="text-lg font-semibold text-gray-800 mb-4">Horarios</h3>
    <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 text-left">Día</th>
                <th class="py-2 px-4 text-left">Inicio</th>
                <th class="py-2 px-4 text-left">Fin</th>
                <th class="py-2 px-4 text-left">Tipo de institución</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($declaracion->horarios as $h)
                <tr class="border-t border-gray-200">
                    <td class="py-2 px-4">{{ $h->dia }}</td>
                    <td class="py-2 px-4">{{ $h->hora_inicio }}</td>
                    <td class="py-2 px-4">{{ $h->hora_fin }}</td>
                    <td class="py-2 px-4">{{ $h->tipo === 'ucr' ? 'UCR' : 'Otra institución pública/privada' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-end mt-8 space-x-4">
        <a href="{{ route('declaraciones.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow-md transition">
           Volver
        </a>
        <a href="{{ route('declaraciones.exportar', $declaracion->id_declaracion) }}"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition">
           Exportar a Excel
        </a>
    </div>
</div>
@endsection
