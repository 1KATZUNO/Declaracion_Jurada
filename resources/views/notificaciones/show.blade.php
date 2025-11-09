@extends('layout')

@section('titulo', 'Detalle de Notificación')

@section('content')
<div class="w-full">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">
        {{-- Encabezado --}}
        <div class="px-8 py-6 bg-blue-600 rounded-t-2xl flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">
                    Detalle de la Notificación
                </h1>
                <p class="text-sm text-blue-100">
                    Información específica de la notificación seleccionada.
                </p>
            </div>

            <a href="{{ route('notificaciones.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white text-blue-600 text-xs font-medium rounded-xl shadow hover:bg-blue-50">
                ← Volver al listado
            </a>
        </div>

        @php
            $user = $notificacion->notifiable;
            $msg  = $notificacion->data['message'] ?? 'Notificación del sistema';
            $url  = $notificacion->data['url'] ?? null;
        @endphp

        {{-- Contenido --}}
        <div class="px-8 py-6 space-y-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Usuario destinatario</p>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $user?->nombre_completo ?? 'Usuario' }}
                    </p>
                    @if($user?->correo)
                        <p class="text-xs text-gray-500">{{ $user->correo }}</p>
                    @endif
                </div>

                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase mb-1">Fecha de envío</p>
                    <p class="text-sm text-gray-800">
                        {{ optional($notificacion->created_at)->format('d/m/Y H:i') ?? '-' }}
                    </p>
                    @if($notificacion->created_at)
                        <p class="text-[10px] text-gray-400">
                            {{ $notificacion->created_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs text-gray-500 uppercase mb-2">Mensaje</p>
                <p class="text-sm text-gray-800 leading-relaxed">
                    {{ $msg }}
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase mb-1">Estado</p>
                    @if($notificacion->read_at)
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700">
                            Leída el {{ $notificacion->read_at->format('d/m/Y H:i') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] rounded-full bg-yellow-100 text-yellow-700">
                            No leída
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if($url)
                        <a href="{{ $url }}"
                           class="px-4 py-2 text-xs bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                            Ir al recurso relacionado
                        </a>
                    @endif

                    <form action="{{ route('notificaciones.destroy', $notificacion->id) }}"
                          method="POST"
                          onsubmit="return confirm('¿Eliminar esta notificación?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 text-xs bg-red-50 text-red-500 rounded-xl hover:bg-red-100">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
