@extends('layout')

@section('content')
<div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Declaraciones juradas</h2>
        <a href="{{ route('declaraciones.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
           + Nueva declaración
        </a>
    </div>

    @if(session('ok'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('ok') }}
        </div>
    @endif

    @if($declaraciones->isEmpty())
        <p class="text-gray-500 text-center">No hay declaraciones registradas aún.</p>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 text-left">#</th>
                    <th class="py-2 px-4 text-left">Funcionario</th>
                    <th class="py-2 px-4 text-left">Unidad</th>
                    <th class="py-2 px-4 text-left">Cargo</th>
                    <th class="py-2 px-4 text-left">Formulario</th>
                    <th class="py-2 px-4 text-left">Fechas</th>
                    <th class="py-2 px-4 text-left">Horas totales</th>
                    <th class="py-2 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($declaraciones as $d)
                    <tr class="border-t border-gray-200 hover:bg-gray-50 transition">
                        <td class="py-2 px-4">{{ $d->id_declaracion }}</td>
                        <td class="py-2 px-4">
                            {{ $d->usuario->nombre ?? '' }} {{ $d->usuario->apellido ?? '' }}
                        </td>
                        <td class="py-2 px-4">{{ $d->unidad->nombre ?? '' }}</td>
                        <td class="py-2 px-4">{{ $d->cargo->nombre ?? '' }}</td>
                        <td class="py-2 px-4">{{ $d->formulario->titulo ?? '' }}</td>
                        <td class="py-2 px-4">
                            {{ \Carbon\Carbon::parse($d->fecha_desde)->format('d/m/Y') }} —
                            {{ \Carbon\Carbon::parse($d->fecha_hasta)->format('d/m/Y') }}
                        </td>
                        <td class="py-2 px-4 text-center">{{ $d->horas_totales }}</td>
                        <td class="py-2 px-4 space-x-2">
                            <a href="{{ route('declaraciones.show', $d->id_declaracion) }}"
                               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded shadow">
                               Ver
                            </a>
                            <a href="{{ route('declaraciones.edit', $d->id_declaracion) }}"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded shadow">
                               Editar
                            </a>
                            <a href="{{ route('declaraciones.exportar', $d->id_declaracion) }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded shadow">
                               Excel
                            </a>
                            <form action="{{ route('declaraciones.destroy', $d->id_declaracion) }}" method="POST"
                                  class="inline" onsubmit="return confirm('¿Eliminar esta declaración?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded shadow">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
