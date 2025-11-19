@extends('layout')
@csrf
@section('content')
<div class="container mx-auto w-full max-w-6xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Detalle del Documento</h2>
            <p class="text-blue-100 text-sm mt-1">Información del documento y resumen de la declaración</p>
        </div>

        <div class="p-2 sm:p-4 md:p-8">
            <!-- Información del Documento -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información del Documento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Archivo</label>
                        <p class="text-sm text-gray-900 font-medium">{{ basename($doc->archivo) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Formato</label>
                        <p class="text-sm text-gray-900">{{ strtoupper($doc->formato) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fecha de generación</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($doc->fecha_generacion)->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            @if($doc->declaracion)
            <!-- Información del funcionario -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información del Funcionario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nombre completo</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $doc->declaracion->usuario->nombre }} {{ $doc->declaracion->usuario->apellido }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Identificación</label>
                        <p class="text-sm text-gray-900">{{ $doc->declaracion->usuario->identificacion }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sede</label>
                        <p class="text-sm text-gray-900">{{ $doc->declaracion->unidad->sede->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Unidad académica</label>
                        <p class="text-sm text-gray-900">{{ $doc->declaracion->unidad->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Información del Formulario -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información del Formulario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Formulario</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $doc->declaracion->formulario->titulo ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fecha de envío</label>
                        <p class="text-sm text-gray-900">{{ $doc->declaracion->fecha_envio ? \Carbon\Carbon::parse($doc->declaracion->fecha_envio)->format('d/m/Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Período</label>
                        <p class="text-sm text-gray-900">
                            {{ $doc->declaracion->fecha_desde ? \Carbon\Carbon::parse($doc->declaracion->fecha_desde)->format('d/m/Y') : 'N/A' }} - 
                            {{ $doc->declaracion->fecha_hasta ? \Carbon\Carbon::parse($doc->declaracion->fecha_hasta)->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Horas totales</label>
                        <p class="text-sm text-gray-900 font-semibold">{{ $doc->declaracion->horas_totales ?? '0' }} horas</p>
                    </div>
                </div>
            </div>

            <!-- Horarios -->
            @php
                $horarios = $doc->declaracion->horarios ?? collect();
                $horariosUCR = $horarios->where('tipo', 'ucr');
                $horariosExternos = $horarios->where('tipo', 'externo')->filter(function($h) {
                    return !empty($h->lugar) && !empty($h->hora_inicio) && !empty($h->hora_fin);
                });
            @endphp

            @if($horariosUCR->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios UCR</h3>
                
                <div class="mb-6">
                    <div class="text-md font-semibold text-gray-800 mb-3 px-4 py-2 bg-blue-50 rounded-lg">
                        @php
                            $horasTotalesUCR = 0;
                            foreach($horariosUCR as $h) {
                                if($h->hora_inicio && $h->hora_fin) {
                                    $inicio = strtotime($h->hora_inicio);
                                    $fin = strtotime($h->hora_fin);
                                    $horasTotalesUCR += ($fin - $inicio) / 3600;
                                }
                            }
                            
                            $fraccion = '';
                            switch($horasTotalesUCR) {
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
                                    {{ $doc->declaracion->cargo->nombre ?? 'Sin cargo asignado' }}
                                </span>
                            </div>
                            @if($horasTotalesUCR > 0)
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Jornada: {{ $fraccion }} ({{ $horasTotalesUCR }}h semanales)
                                </span>
                            </div>
                            @endif
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
                                @php
                                    $horariosUCR = collect($horariosUCR)->filter(function($h) {
                                        return $h && isset($h->hora_inicio, $h->hora_fin);
                                    });
                                @endphp

                                @foreach ($horariosUCR as $h)
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
            </div>
            @endif

            @if($horariosExternos->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios en Otras Instituciones</h3>
                
                @php
                    $horariosPorInstitucion = [];
                    foreach($horariosExternos as $h) {
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
                                $horasTotalesInst = 0;
                                foreach($instData['horarios'] as $h) {
                                    if($h->hora_inicio && $h->hora_fin) {
                                        $inicio = strtotime($h->hora_inicio);
                                        $fin = strtotime($h->hora_fin);
                                        $horasTotalesInst += ($fin - $inicio) / 3600;
                                    }
                                }
                                
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

            <!-- Observaciones -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Observaciones adicionales</h3>
                <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm text-gray-800">
                    {{ $doc->declaracion->observaciones_adicionales ?? 'Sin observaciones adicionales.' }}
                </div>
            </div>
            @endif

            <!-- Botones de acción -->
            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('documentos.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                   Volver
                </a>
                <a href="{{ route('documentos.download', ['id' => $doc->id_documento]) }}"
                   class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm transition-colors">
                   Descargar Documento
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
