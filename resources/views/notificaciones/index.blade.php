@extends('layout')

@section('titulo', 'Notificaciones del Sistema')

@section('content')
<div class="w-full">
    {{-- Encabezado tipo dashboard --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">
        <div class="px-8 py-6 bg-blue-600 rounded-t-2xl flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">
                    Notificaciones del Sistema
                </h1>
                <p class="text-sm text-blue-100">
                    Gestión y visualización de las notificaciones enviadas a los usuarios.
                </p>
            </div>
        </div>

        {{-- Barra superior con info --}}
        <div class="px-8 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="text-xs text-gray-500">
                @if($notificaciones->total() > 0)
                    Mostrando
                    <span class="font-semibold">{{ $notificaciones->firstItem() }}</span>
                    -
                    <span class="font-semibold">{{ $notificaciones->lastItem() }}</span>
                    de
                    <span class="font-semibold">{{ $notificaciones->total() }}</span>
                    notificaciones.
                @else
                    No hay notificaciones registradas en el sistema.
                @endif
            </div>

            @if($notificaciones->total() > 0 && Route::has('notificaciones.marcar-todas'))
                <form action="{{ route('notificaciones.marcar-todas') }}" method="POST" class="flex justify-end">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Marcar todas como leídas
                    </button>
                </form>
            @endif
        </div>

        {{-- Tabla de notificaciones --}}
        <div class="px-4 pb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-700">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] uppercase text-gray-500">
                            <th class="px-6 py-3 text-left font-semibold">Usuario</th>
                            <th class="px-6 py-3 text-left font-semibold">Mensaje</th>
                            <th class="px-6 py-3 text-left font-semibold">Fecha envío</th>
                            <th class="px-6 py-3 text-left font-semibold text-center">Estado</th>
                            <th class="px-6 py-3 text-left font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($notificaciones as $n)
                        @php
                            $user = $n->notifiable;
                            $msg  = $n->data['message'] ?? 'Notificación del sistema';
                            $fecha = $n->created_at ? $n->created_at->format('d/m/Y H:i') : '-';
                            $isRead = !is_null($n->read_at);
                        @endphp
                        <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-6 py-3 align-top">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900">
                                        {{ $user?->nombre_completo ?? 'Usuario' }}
                                    </span>
                                    @if($user?->correo)
                                        <span class="text-[11px] text-gray-500">{{ $user->correo }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 align-top">
                                <span class="text-gray-800">
                                    {{ $msg }}
                                </span>
                            </td>
                            <td class="px-6 py-3 align-top">
                                <div class="flex flex-col">
                                    <span>{{ $fecha }}</span>
                                    @if($n->created_at)
                                        <span class="text-[10px] text-gray-400">
                                            {{ $n->created_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 align-top text-center">
                                @if($isRead)
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700">
                                        Leída
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] rounded-full bg-yellow-100 text-yellow-700">
                                        No leída
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 align-top text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('notificaciones.show', $n->id) }}"
                                       class="text-xs text-blue-600 hover:underline">
                                        Ver
                                    </a>
                                    <form action="{{ route('notificaciones.destroy', $n->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar esta notificación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-500 hover:underline">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500">
                                No hay notificaciones registradas por el momento.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($notificaciones->hasPages())
                <div class="mt-4">
                    {{ $notificaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
