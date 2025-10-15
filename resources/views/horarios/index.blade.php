@extends('layout')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Lista de Horarios</h2>
        <a href="{{ route('horarios.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            + Nuevo Horario
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 text-left">#</th>
                <th class="py-2 px-4 text-left">Tipo</th>
                <th class="py-2 px-4 text-left">Día</th>
                <th class="py-2 px-4 text-left">Inicio</th>
                <th class="py-2 px-4 text-left">Fin</th>
                <th class="py-2 px-4 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($horarios as $h)
                <tr class="border-t border-gray-200 hover:bg-gray-50 transition">
                    <td class="py-2 px-4">{{ $h->id_horario }}</td>
                    <td class="py-2 px-4">
                        {{ $h->tipo === 'ucr' ? 'UCR' : 'Otra institución' }}
                    </td>
                    <td class="py-2 px-4">{{ $h->dia }}</td>
                    <td class="py-2 px-4">{{ $h->hora_inicio }}</td>
                    <td class="py-2 px-4">{{ $h->hora_fin }}</td>
                    <td class="py-2 px-4 flex space-x-2">
                        <form action="{{ route('horarios.destroy', $h->id_horario) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar este horario?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg shadow">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">No hay horarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $horarios->links() }}
    </div>
</div>
@endsection
