@extends('layout')

@section('titulo', 'Detalle de Actividad')

@section('contenido')
<div class="container mx-auto w-full max-w-4xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Detalle de Actividad</h2>
            <p class="text-blue-50 text-sm mt-1">Información completa del registro</p>
        </div>

        <div class="p-8">
            <div class="space-y-6">
                {{-- Información básica --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora</label>
                        <p class="text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                        <p class="text-gray-900">
                            {{ $log->usuario ? $log->usuario->nombre . ' ' . $log->usuario->apellido : 'Sistema' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                        <p class="text-gray-900">{{ $log->correo_usuario ?? optional($log->usuario)->correo ?? optional($log->usuario)->email ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($log->accion == 'crear') bg-green-100 text-green-800
                            @elseif($log->accion == 'editar') bg-yellow-100 text-yellow-800
                            @elseif($log->accion == 'eliminar') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($log->accion) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Módulo</label>
                        <p class="text-gray-900">{{ $log->modulo }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <p class="text-gray-900">{{ $log->descripcion }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección IP</label>
                        <p class="text-gray-900">{{ $log->ip_address ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Registro</label>
                        <p class="text-gray-900">{{ $log->id_registro ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- User Agent --}}
                @if($log->user_agent)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Navegador/Dispositivo</label>
                    <p class="text-gray-900 text-sm break-all">{{ $log->user_agent }}</p>
                </div>
                @endif

                {{-- Datos anteriores --}}
                @if($log->datos_anteriores)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Datos Anteriores</label>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <pre class="text-xs text-gray-800 overflow-x-auto">{{ json_encode($log->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
                @endif

                {{-- Datos nuevos --}}
                @if($log->datos_nuevos)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Datos Nuevos</label>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <pre class="text-xs text-gray-800 overflow-x-auto">{{ json_encode($log->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('actividad-logs.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
