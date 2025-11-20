@extends('layout')
 @csrf
@section('titulo', 'Cargos')

@section('contenido')
<div class="container mx-auto w-full max-w-7xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-white">Cargos del Personal</h2>
                <p class="text-blue-100 text-sm mt-1">Gestión de cargos de la UCR</p>
            </div>
            <a href="{{ route('cargos.create') }}"
               class="px-5 py-2.5 text-sm font-medium text-blue-700 bg-white border border-transparent rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors shadow-sm">
               Nuevo Cargo
            </a>
        </div>

        <div class="p-2 sm:p-4 md:p-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nombre</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Descripción</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($cargos as $c)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm text-gray-900 font-medium">{{ $c->nombre }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ Str::limit($c->descripcion, 80) }}</td>
                            <td class="py-4 px-4 text-sm">
                                <div class="flex gap-2">
                                    <a href="{{ route('cargos.edit', $c->id_cargo) }}"
                                       class="px-3 py-1.5 text-xs font-medium text-yellow-700 bg-yellow-50 border border-yellow-300 rounded hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors">
                                       Editar
                                    </a>
                                    <form action="{{ route('cargos.destroy', $c->id_cargo) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Eliminar este cargo?')">
                                        @csrf @method('DELETE')
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
        </div>
    </div>
</div>
@endsection
