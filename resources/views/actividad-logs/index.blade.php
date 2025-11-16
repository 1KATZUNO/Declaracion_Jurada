@extends('layout')

@section('titulo', 'Registro de Actividades')

@section('contenido')
<div class="container mx-auto w-full max-w-7xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Registro de Actividades</h2>
            <p class="text-blue-100 text-sm mt-1">Historial de acciones del sistema</p>
        </div>

        <div class="p-8">
            {{-- Filtros --}}
            <form method="GET" action="{{ route('actividad-logs.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                        <select name="accion" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Todas</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>
                                    {{ ucfirst($accion) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Módulo</label>
                        <select name="modulo" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Todos</option>
                            @foreach($modulos as $modulo)
                                <option value="{{ $modulo }}" {{ request('modulo') == $modulo ? 'selected' : '' }}>
                                    {{ $modulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        Filtrar
                    </button>
                    <a href="{{ route('actividad-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300">
                        Limpiar filtros
                    </a>
                </div>
            </form>

            {{-- Tabla de logs --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase">Fecha/Hora</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase">Usuario</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase">Acción</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase">Módulo</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase">Descripción</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase">IP</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-sm text-gray-900">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-900">
                                {{ $log->usuario ? $log->usuario->nombre . ' ' . $log->usuario->apellido : 'Sistema' }}
                            </td>
                            <td class="py-3 px-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    @if($log->accion == 'crear') bg-green-100 text-green-800
                                    @elseif($log->accion == 'editar') bg-yellow-100 text-yellow-800
                                    @elseif($log->accion == 'eliminar') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($log->accion) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $log->modulo }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ Str::limit($log->descripcion, 50) }}</td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $log->ip_address }}</td>
                            <td class="py-3 px-4 text-sm">
                                <a href="{{ route('actividad-logs.show', $log->id_actividad) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    Ver detalles
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                No hay registros de actividad
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
