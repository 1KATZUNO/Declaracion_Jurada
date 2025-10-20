@extends('layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-white">Lista de Horarios</h2>
                <p class="text-blue-100 text-sm mt-1">Gestión de horarios registrados</p>
            </div>
            <a href="{{ route('horarios.create') }}"
               class="px-5 py-2.5 text-sm font-medium text-blue-700 bg-white border border-transparent rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors shadow-sm">
                Nuevo Horario
            </a>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">#</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Tipo</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Día</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora inicio</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hora fin</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($horarios as $h)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-sm text-gray-900">{{ $h->id_horario }}</td>
                                <td class="py-4 px-4 text-sm text-gray-600">
                                    {{ $h->tipo === 'ucr' ? 'UCR' : 'Otra institución' }}
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-900 font-medium">{{ $h->dia }}</td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ $h->hora_inicio }}</td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ $h->hora_fin }}</td>
                                <td class="py-4 px-4 text-sm">
                                    <form action="{{ route('horarios.destroy', $h->id_horario) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este horario?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-300 rounded hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-500">No hay horarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $horarios->links() }}
    </div>
</div>
@endsection
