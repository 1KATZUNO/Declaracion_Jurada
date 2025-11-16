@extends('layout')
@csrf
@section('titulo', 'Documentos Generados')

@section('contenido')
<div class="container mx-auto w-full max-w-7xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Historial de Declaraciones</h2>
            <p class="text-blue-100 text-sm mt-1">Archivos generados del sistema</p>
        </div>

        <div class="p-2 sm:p-4 md:p-8">

            <!-- Buscador -->
            <form method="GET" action="{{ route('documentos.index') }}" class="mb-6">
                <div class="flex items-center gap-3">
                    <input type="text" name="buscar"
                           value="{{ request('buscar') }}"
                           placeholder="Buscar por profesor..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Buscar
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Profesor</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Archivo</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Formato</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha Generación</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                        @foreach ($documentos as $doc)
                            @php
                                $fileUrl = (strpos($doc->archivo, 'public/') === 0)
                                    ? \Illuminate\Support\Facades\Storage::url($doc->archivo)
                                    : asset($doc->archivo);
                            @endphp

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

                                        <!-- Botón Descargar -->
                                        <a href="{{ $fileUrl }}"
                                           download="{{ basename($doc->archivo) }}" 
                                           target="_blank" 
                                           rel="noopener"
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
