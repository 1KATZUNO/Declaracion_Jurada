@extends('layout')
@csrf
@section('titulo', 'Documentos Generados')

@section('contenido')
<div class="container mx-auto w-full max-w-7xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Historial de Declaraciones</h2>
            <p class="text-blue-50 text-sm mt-1">Archivos generados del sistema</p>
        </div>

        <div class="p-2 sm:p-4 md:p-8">

            <!-- Filtros -->
            <form method="GET" action="{{ route('documentos.index') }}" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    
                    @if($user->rol !== 'funcionario')
                    <!-- Filtro por nombre (solo admin) -->
                    <input type="text" name="buscar"
                           value="{{ request('buscar') }}"
                           placeholder="Buscar por funcionario..."
                           class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @endif

                    <!-- Filtro por fecha desde -->
                    <input type="date" name="fecha_desde"
                           value="{{ request('fecha_desde') }}"
                           placeholder="Fecha desde"
                           class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <!-- Filtro por fecha hasta -->
                    <input type="date" name="fecha_hasta"
                           value="{{ request('fecha_hasta') }}"
                           placeholder="Fecha hasta"
                           class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Buscar
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Funcionario</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Archivo</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Formato</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha Generación</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                        @foreach ($documentos as $doc)

                            <tr class="hover:bg-gray-50 transition-colors">

                                <td class="py-4 px-4 text-sm text-gray-900 font-medium">
                                    {{ $doc->declaracion->usuario->nombre }} {{ $doc->declaracion->usuario->apellido }}
                                </td>

                                <td class="py-4 px-4 text-sm text-gray-600">
                                    {{ basename($doc->archivo) }}
                                </td>

                                <td class="py-4 px-4 text-sm text-gray-600">
                                    {{ $doc->formato }}
                                </td>

                                <td class="py-4 px-4 text-sm text-gray-600">
                                    {{ $doc->fecha_generacion }}
                                </td>

                                <td class="py-4 px-4 text-sm">
                                    <div class="flex gap-2">

                                        <!-- Botón Ver Detalles -->
                                        <a href="{{ route('documentos.show', ['documento' => $doc->id_documento]) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                            Ver detalles
                                        </a>

                                        <!-- Botón Descargar -->
                                        <a href="{{ route('documentos.download', ['id' => $doc->id_documento]) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-300 rounded hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                                            Descargar
                                        </a>

                                        <!-- Botón Eliminar -->
                                        <form action="{{ route('documentos.destroy', ['documento' => $doc->id_documento]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('¿Eliminar este documento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-300 rounded hover:bg-red-100">
                                                Eliminar
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
