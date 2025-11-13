@extends('layout')

@section('titulo','Comentarios (Admin)')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

  {{-- Header --}}
  <div class="px-6 py-4 bg-blue-600 text-white flex items-center justify-between">
    <div>
      <h1 class="text-xl font-semibold">Comentarios</h1>
      <p class="text-sm text-blue-100">Listado global de hilos creados por funcionarios</p>
    </div>
    <a href="{{ route('admin.comentarios.index') }}"
       class="text-xs bg-white/10 px-3 py-1.5 rounded hover:bg-white/20">
      Actualizar
    </a>
  </div>

  {{-- Tabla --}}
  <div class="p-6 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-50 text-[11px] uppercase text-gray-600">
          <th class="px-3 py-2 text-left font-semibold">Autor</th>
          <th class="px-3 py-2 text-left font-semibold">Título</th>
          <th class="px-3 py-2 text-left font-semibold">Estado</th>
          <th class="px-3 py-2 text-left font-semibold">Fecha</th>
          <th class="px-3 py-2 text-center font-semibold">Acciones</th>
        </tr>
      </thead>

      <tbody>
        @forelse($comentarios as $c)
          @php
            $isOpen = ($c->estado ?? 'abierto') === 'abierto';
            $fecha  = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
          @endphp

          <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }} border-b border-gray-100">

            {{-- Autor --}}
            <td class="px-3 py-2 align-top">
              {{ $c->autor?->nombre_completo ?? 'Usuario' }}
              @if($c->autor?->correo)
                <div class="text-[11px] text-gray-500">{{ $c->autor->correo }}</div>
              @endif
            </td>

            {{-- Título + preview --}}
            <td class="px-3 py-2 align-top">
              {{ $c->titulo ?? 'Sin título' }}
              <div class="text-[12px] text-gray-500 line-clamp-1">
                {{ \Illuminate\Support\Str::limit($c->mensaje ?? '', 90) }}
              </div>
            </td>

            {{-- Estado --}}
            <td class="px-3 py-2 align-top">
              @if($isOpen)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-green-100 text-green-700">
                  Abierto
                </span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-gray-200 text-gray-700">
                  Cerrado
                </span>
              @endif
            </td>

            {{-- Fecha --}}
            <td class="px-3 py-2 align-top">
              <div>{{ $fecha }}</div>
              @if($c->created_at)
                <div class="text-[10px] text-gray-400">{{ $c->created_at->diffForHumans() }}</div>
              @endif
            </td>

            {{-- Acciones --}}
            <td class="px-3 py-2 align-top">
              <div class="flex items-center justify-center gap-2">

                {{-- Abrir hilo --}}
                <a class="text-blue-600 text-xs hover:underline"
                   href="{{ route('comentarios.show', $c->id_comentario) }}">
                  Abrir hilo
                </a>

                {{-- Cerrar SOLO si está abierto --}}
                @if($isOpen)
                  <form action="{{ route('admin.comentarios.estado', $c->id_comentario) }}"
                        method="POST"
                        onsubmit="return confirm('¿Cerrar este hilo?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="cerrado">
                    <button type="submit"
                      class="text-xs px-2 py-1 rounded border border-red-300 text-red-700 bg-red-50 hover:bg-red-100">
                      Cerrar
                    </button>
                  </form>
                @endif

                {{-- Cuando está cerrado NO mostramos nada más --}}
              </div>
            </td>

          </tr>

        @empty
          <tr>
            <td colspan="5" class="px-3 py-8 text-center text-gray-500">
              Sin registros.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Paginación --}}
    <div class="mt-4">
      {{ $comentarios->links() }}
    </div>
  </div>
</div>
@endsection
