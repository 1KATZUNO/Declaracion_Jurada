@extends('layout')
 @csrf
@section('content')
<div class="container mx-auto w-full max-w-7xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-white">Declaraciones juradas</h2>
                <p class="text-blue-100 text-sm mt-1">Gestión de declaraciones registradas</p>
            </div>
            <a href="{{ route('declaraciones.create') }}"
               class="px-5 py-2.5 text-sm font-medium text-blue-700 bg-white border border-transparent rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors shadow-sm">
               Nueva declaración
            </a>
        </div>

        <div class="p-2 sm:p-4 md:p-8">
            @include('components.flash')

            @if($declaraciones->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500 text-base">No hay declaraciones registradas aún.</p>
                    <p class="text-gray-400 text-sm mt-2">Comienza creando una nueva declaración.</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">#</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Funcionario</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Unidad</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Formulario</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($declaraciones as $d)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-sm text-gray-900 font-bold">{{ $d->id_declaracion ?? 'N/A' }}</td>
                                <td class="py-4 px-4 text-sm text-gray-900 font-medium">
                                    {{ optional($d->usuario)->nombre ?? 'Sin usuario' }} {{ optional($d->usuario)->apellido ?? '' }}
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ optional($d->unidad)->nombre ?? 'Sin unidad' }}</td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ optional($d->formulario)->titulo ?? 'Sin formulario' }}</td>
                                <td class="py-4 px-4 text-sm">
                                    <div class="flex gap-2">
                                        <a href="{{ route('declaraciones.show', $d->id_declaracion) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                           Ver
                                        </a>
                                        <a href="{{ route('declaraciones.edit', $d->id_declaracion) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-50 border border-yellow-300 rounded hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors">
                                           Editar
                                        </a>
                                        <a href="{{ route('declaraciones.exportar', $d->id_declaracion) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-300 rounded hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                                           Excel
                                        </a>
                                        <a href="{{ route('declaraciones.pdf', $d->id_declaracion) }}"
                                           class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                           PDF
                                        </a>
                                        <form action="{{ route('declaraciones.destroy', $d->id_declaracion) }}" method="POST"
                                              class="inline" onsubmit="return confirm('¿Eliminar esta declaración?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-300 rounded hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
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
            
            <!-- Pagination Links -->
            <div class="mt-6">
                {{ $declaraciones->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
