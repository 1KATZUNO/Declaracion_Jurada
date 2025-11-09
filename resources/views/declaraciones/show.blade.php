@extends('layout')
 @csrf
@section('content')
<div class="container mx-auto w-full max-w-6xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Detalle de la Declaración Jurada</h2>
            <p class="text-blue-100 text-sm mt-1">Información completa de la declaración</p>
        </div>

        <div class="p-2 sm:p-4 md:p-8">
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
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Formulario</label>
                        <p class="text-sm text-gray-900">{{ $declaracion->formulario->nombre }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Horas totales</label>
                        <p class="text-sm text-gray-900 font-semibold">{{ $declaracion->horas_totales }} horas</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fecha de envío</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($declaracion->fecha_envio)->format('d/m/Y H:i') }}</p>
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

            <!-- Horarios UCR -->
            @php
                $horariosUCR = $declaracion->horarios->where('tipo', 'ucr');
                $horariosExternos = $declaracion->horarios->where('tipo', 'externo');
            @endphp

            @if($horariosUCR->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios UCR</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Cargo</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Día</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora inicio</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora fin</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Vigencia</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($horariosUCR as $h)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">
                                        {{ $h->cargo ? $h->cargo->nombre : 'Sin cargo asignado' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ $h->dia }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_inicio }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_fin }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        @if($h->desde && $h->hasta)
                                            {{ \Carbon\Carbon::parse($h->desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($h->hasta)->format('d/m/Y') }}
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Horarios Instituciones Externas -->
            @if($horariosExternos->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios en Otras Instituciones</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Institución</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Cargo</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Día</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora inicio</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora fin</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Vigencia</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($horariosExternos as $h)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $h->lugar ?? 'Sin institución' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ $h->cargo ?? 'Sin cargo' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-900">{{ $h->dia }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_inicio }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_fin }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        @if($h->desde && $h->hasta)
                                            {{ \Carbon\Carbon::parse($h->desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($h->hasta)->format('d/m/Y') }}
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
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
            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
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
