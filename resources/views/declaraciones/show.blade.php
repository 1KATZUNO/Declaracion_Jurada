@extends('layout')
 @csrf
@section('content')
<div class="container mx-auto w-full max-w-6xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
        <div class="bg-blue-600 px-8 py-6">
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
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sede</label>
                        <p class="text-sm text-gray-900">{{ $declaracion->unidad->sede->nombre }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Unidad académica</label>
                        <p class="text-sm text-gray-900">{{ $declaracion->unidad->nombre }}</p>
                    </div>
                </div>
            </div>

            <!-- Información del Formulario -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información del Formulario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Formulario</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $declaracion->formulario->titulo }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fecha de envío</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($declaracion->fecha_envio)->format('d/m/Y H:i') }}</p>
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
                
                @php
                    // Usar el cargo de la declaración para todos los horarios UCR
                    $cargoDeclaracion = $declaracion->cargo ? $declaracion->cargo->nombre : 'Sin cargo asignado';
                    $horariosPorCargo = [
                        $cargoDeclaracion => [
                            'cargo' => $cargoDeclaracion,
                            'horarios' => $horariosUCR
                        ]
                    ];
                @endphp

                @foreach($horariosPorCargo as $cargoData)
                    <div class="mb-6">
                        <div class="text-md font-semibold text-gray-800 mb-3 px-4 py-2 bg-blue-50 rounded-lg">
                            @php
                                $primerHorario = $cargoData['horarios'][0];
                                
                                // Calcular horas totales de este cargo
                                $horasTotalesCargo = 0;
                                foreach($cargoData['horarios'] as $h) {
                                    if($h->hora_inicio && $h->hora_fin) {
                                        $inicio = strtotime($h->hora_inicio);
                                        $fin = strtotime($h->hora_fin);
                                        $horasTotalesCargo += ($fin - $inicio) / 3600;
                                    }
                                }
                                
                                // Determinar la fracción basada en las horas
                                $fraccion = '';
                                switch($horasTotalesCargo) {
                                    case 5: $fraccion = '1/8'; break;
                                    case 10: $fraccion = '1/4'; break;
                                    case 15: $fraccion = '3/8'; break;
                                    case 20: $fraccion = '1/2'; break;
                                    case 30: $fraccion = '3/4'; break;
                                    case 40: $fraccion = 'TC'; break;
                                    default: $fraccion = 'Personalizada';
                                }
                            @endphp
                            
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">
                                        UCR
                                    </span>
                                    <span class="text-base font-semibold text-gray-900">
                                        {{ $cargoData['cargo'] }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    @if($horasTotalesCargo > 0)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Jornada: {{ $fraccion }} ({{ $horasTotalesCargo }}h semanales)
                                        </span>
                                    @endif
                                    
                                    @if($primerHorario->desde && $primerHorario->hasta)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Vigencia: {{ \Carbon\Carbon::parse($primerHorario->desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($primerHorario->hasta)->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Día</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora inicio</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora fin</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Horas</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($cargoData['horarios'] as $h)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 text-sm text-gray-900">{{ $h->dia }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_inicio }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_fin }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-900 font-semibold">
                                                @php
                                                    $inicio = strtotime($h->hora_inicio);
                                                    $fin = strtotime($h->hora_fin);
                                                    $horas = ($fin - $inicio) / 3600;
                                                @endphp
                                                {{ $horas }}h
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Horarios Instituciones Externas -->
            @php
                // Filtrar horarios externos que tengan datos reales (no vacíos)
                $horariosExternosValidos = $horariosExternos->filter(function($h) {
                    return !empty($h->lugar) && !empty($h->hora_inicio) && !empty($h->hora_fin);
                });
            @endphp
            
            @if($horariosExternosValidos->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios en Otras Instituciones</h3>
                
                @php
                    // Agrupar horarios externos válidos por institución
                    $horariosPorInstitucion = [];
                    foreach($horariosExternosValidos as $h) {
                        $instKey = $h->lugar ?? 'Sin institución';
                        if (!isset($horariosPorInstitucion[$instKey])) {
                            $horariosPorInstitucion[$instKey] = [
                                'institucion' => $instKey,
                                'cargo' => $h->cargo ?? 'Sin cargo',
                                'horarios' => []
                            ];
                        }
                        $horariosPorInstitucion[$instKey]['horarios'][] = $h;
                    }
                @endphp

                @foreach($horariosPorInstitucion as $instData)
                    <div class="mb-6">
                        <div class="text-md font-semibold text-gray-800 mb-3 px-4 py-2 bg-green-50 rounded-lg">
                            @php
                                $primerHorario = $instData['horarios'][0];
                                
                                // Calcular horas totales de esta institución
                                $horasTotalesInst = 0;
                                foreach($instData['horarios'] as $h) {
                                    if($h->hora_inicio && $h->hora_fin) {
                                        $inicio = strtotime($h->hora_inicio);
                                        $fin = strtotime($h->hora_fin);
                                        $horasTotalesInst += ($fin - $inicio) / 3600;
                                    }
                                }
                                
                                // Determinar la fracción basada en las horas
                                $fraccionInst = '';
                                switch($horasTotalesInst) {
                                    case 5: $fraccionInst = '1/8'; break;
                                    case 10: $fraccionInst = '1/4'; break;
                                    case 15: $fraccionInst = '3/8'; break;
                                    case 20: $fraccionInst = '1/2'; break;
                                    case 30: $fraccionInst = '3/4'; break;
                                    case 40: $fraccionInst = 'TC'; break;
                                    default: $fraccionInst = 'Personalizada';
                                }
                            @endphp
                            
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3">
                                        EXTERNA
                                    </span>
                                    <span class="text-base font-semibold text-gray-900">
                                        {{ $instData['institucion'] }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 002 2h4a2 2 0 002-2V6zM8 6V4H4a2 2 0 00-2 2v2a2 2 0 002 2h4V6z"></path>
                                        </svg>
                                        Cargo: {{ $instData['cargo'] }}
                                    </span>
                                    
                                    @if($horasTotalesInst > 0)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Jornada: {{ $fraccionInst }} ({{ $horasTotalesInst }}h semanales)
                                        </span>
                                    @endif
                                    
                                    @if($primerHorario->desde && $primerHorario->hasta)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Vigencia: {{ \Carbon\Carbon::parse($primerHorario->desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($primerHorario->hasta)->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Día</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora inicio</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora fin</th>
                                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Horas</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($instData['horarios'] as $h)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-4 text-sm text-gray-900">{{ $h->dia }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_inicio }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-600">{{ $h->hora_fin }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-900 font-semibold">
                                                @php
                                                    if($h->hora_inicio && $h->hora_fin) {
                                                        $inicio = strtotime($h->hora_inicio);
                                                        $fin = strtotime($h->hora_fin);
                                                        $horas = ($fin - $inicio) / 3600;
                                                        echo $horas . 'h';
                                                    } else {
                                                        echo '-';
                                                    }
                                                @endphp
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
            <!-- ...otras secciones... -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Observaciones adicionales</h3>
                <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm text-gray-800">
                    {{ $declaracion->observaciones_adicionales ?? 'Sin observaciones adicionales.' }}
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
            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('declaraciones.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                   Volver
                </a>
                <a href="{{ route('declaraciones.exportar', $declaracion->id_declaracion) }}"
                   class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm transition-colors">
                   Exportar a Excel
                </a>
                <a href="{{ route('declaraciones.pdf', $declaracion->id_declaracion) }}"
                   class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                   Exportar a PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
