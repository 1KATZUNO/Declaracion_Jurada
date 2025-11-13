@extends('layout')

@section('titulo','Detalle del Comentario')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-4xl mx-auto space-y-6">

  {{-- Encabezado --}}
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">{{ $comentario->titulo ?? 'Sin título' }}</h1>
      <p class="text-sm text-gray-500">
        Por {{ $comentario->autor?->nombre_completo ?? 'Usuario' }}
        · {{ $comentario->created_at->format('d/m/Y H:i') }}
      </p>
    </div>
    <span class="px-2 py-0.5 text-[10px] rounded-full {{ $comentario->estado==='abierto'?'bg-green-100 text-green-700':'bg-gray-200 text-gray-700' }}">
      {{ ucfirst($comentario->estado) }}
    </span>
  </div>

  {{-- Mensaje principal --}}
  <div class="border rounded p-4 bg-gray-50">
    <p class="whitespace-pre-line text-gray-800">{{ $comentario->mensaje }}</p>
  </div>

  {{-- Acciones del funcionario si el hilo está abierto --}}
  @if(session('usuario_rol') !== 'admin'
      && $comentario->estado === 'abierto'
      && $comentario->id_usuario == session('usuario_id'))
    <div class="flex items-center gap-2">
      <a class="text-xs text-amber-600 hover:underline"
         href="{{ route('comentarios.edit',$comentario->id_comentario) }}">
        Editar
      </a>
      <form action="{{ route('comentarios.destroy',$comentario->id_comentario) }}"
            method="POST"
            onsubmit="return confirm('¿Eliminar este comentario?');">
        @csrf
        @method('DELETE')
        <button class="text-xs text-red-600 hover:underline" type="submit">
          Eliminar
        </button>
      </form>
    </div>
  @endif

  {{-- Respuestas --}}
  <div class="space-y-3">
    <h2 class="text-sm font-semibold text-gray-700">Respuestas</h2>
    @forelse($comentario->respuestas as $r)
      <div class="border rounded p-3">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
          <span>{{ $r->autor?->nombre_completo ?? 'Usuario' }}</span>
          <span>{{ $r->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $r->mensaje }}</p>

        @if(session('usuario_rol') === 'admin')
          <form action="{{ route('admin.respuestas.destroy',$r->id_respuesta) }}"
                method="POST"
                class="mt-2 text-right"
                onsubmit="return confirm('¿Eliminar respuesta?');">
            @csrf
            @method('DELETE')
            <button class="text-xs text-red-600 hover:underline">
              Eliminar
            </button>
          </form>
        @endif
      </div>
    @empty
      <p class="text-sm text-gray-500">Aún no hay respuestas.</p>
    @endforelse
  </div>

  {{-- Admin: responder + cerrar/reabrir --}}
  @if(session('usuario_rol') === 'admin')
    @if($comentario->estado === 'abierto')
      <form action="{{ route('admin.comentarios.respuestas.store',$comentario->id_comentario) }}"
            method="POST"
            class="space-y-3">
        @csrf
        <label class="text-sm text-gray-700">Responder</label>
        <textarea name="mensaje"
                  rows="4"
                  class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('mensaje') }}</textarea>
        @error('mensaje')
          <p class="text-red-600 text-xs">{{ $message }}</p>
        @enderror
        <div class="flex justify-end">
          <button class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Enviar
          </button>
        </div>
      </form>
    @endif

    <form action="{{ route('admin.comentarios.estado',$comentario->id_comentario) }}"
          method="POST"
          class="text-right">
      @csrf
      @method('PATCH')
      <input type="hidden"
             name="estado"
             value="{{ $comentario->estado==='abierto' ? 'cerrado' : 'abierto' }}">
      <button class="mt-2 px-3 py-2 border rounded hover:bg-gray-50">
        {{ $comentario->estado==='abierto' ? 'Cerrar hilo' : 'Reabrir hilo' }}
      </button>
    </form>
  @endif

  @if(session('ok'))
    <div class="bg-green-100 text-green-800 text-sm px-3 py-2 rounded mt-4">
      {{ session('ok') }}
    </div>
  @endif
</div>
@endsection
