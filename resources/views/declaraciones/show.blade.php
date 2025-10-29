@extends('layout')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Detalle de la Declaración Jurada</h2>
            <p class="text-blue-100 text-sm mt-1">Información completa de la declaración</p>
        </div>

        <div class="p-8">
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información del funcionario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nombre completo</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $declaracion->usuario->nombre }} {{ $declaracion->usuario->apellido }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Identificación</label>
                        <p class="text-sm text-gray-900">{{ $declaracion->usuario->identificacion }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Unidad académica</label>
                        <p class="text-sm text-gray-900">{{ $declaracion->unidad->nombre }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Cargo</label>
                        <p class="text-sm text-gray-900">{{ $declaracion->cargo->nombre }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Jornada</label>
                        <p class="text-sm text-gray-900">{{ $declaracion->cargo->jornada }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Horas totales</label>
                        <p class="text-sm text-gray-900 font-semibold">{{ $declaracion->horas_totales }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Período</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fecha desde</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($declaracion->fecha_desde)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fecha hasta</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($declaracion->fecha_hasta)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Día</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora inicio</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora fin</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tipo de institución</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($declaracion->horarios as $h)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $h->dia }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_inicio }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_fin }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $h->tipo === 'ucr' ? 'UCR' : 'Otra institución pública/privada' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

<hr class="my-8 border-gray-300">

<div class="grid grid-cols-1 md:grid-cols-2 gap-10 text-center mt-10">
    <div>
        <p class="text-sm font-semibold text-gray-800 mb-8">Firma del funcionario</p>
        <div class="border-t-2 border-gray-500 w-3/4 mx-auto"></div>
        <p class="text-xs text-gray-500 mt-2">{{ $declaracion->usuario->nombre }} {{ $declaracion->usuario->apellido }}</p>
    </div>
    <div>
        <p class="text-sm font-semibold text-gray-800 mb-8">Firma del encargado</p>
        <div class="border-t-2 border-gray-500 w-3/4 mx-auto"></div>
        <p class="text-xs text-gray-500 mt-2">Coordinación UCR</p>
    </div>
</div>



            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('declaraciones.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                   Volver
                </a>
                <a href="{{ route('declaraciones.exportar', $declaracion->id_declaracion) }}"
                   class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm transition-colors">
                   Exportar a Excel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
