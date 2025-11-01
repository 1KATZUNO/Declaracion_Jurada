@extends('layout')
 @csrf
@section('titulo', 'Notificaciones')

@section('contenido')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-white">Notificaciones del Sistema</h2>
                <p class="text-blue-100 text-sm mt-1">Gestión de notificaciones enviadas</p>
            </div>
            <a href="{{ route('notificaciones.create') }}"
               class="px-5 py-2.5 text-sm font-medium text-blue-700 bg-white border border-transparent rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white transition-colors shadow-sm">
               Nueva Notificación
            </a>
        </div>

        <div class="p-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Usuario</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Mensaje</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha Envío</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($notificaciones as $n)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 text-sm text-gray-900 font-medium">{{ $n->usuario->nombre }} {{ $n->usuario->apellido }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ Str::limit($n->mensaje, 80) }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ $n->fecha_envio }}</td>
                            <td class="py-4 px-4 text-sm">
                                @php
                                    $color = match($n->estado) {
                                        'pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'enviada' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'leída' => 'bg-green-100 text-green-800 border-green-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                @endphp
                                <span class="px-2 py-1 border rounded-md text-xs font-medium {{ $color }}">{{ ucfirst($n->estado) }}</span>
                            </td>
                            <td class="py-4 px-4 text-sm">
                                <form action="{{ route('notificaciones.destroy', $n->id_notificacion) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta notificación?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-300 rounded hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                        Eliminar
                                    </button>
                                </form>
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
