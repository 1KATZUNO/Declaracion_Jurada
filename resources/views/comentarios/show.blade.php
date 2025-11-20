@extends('layout')

@section('titulo','Detalle del Comentario')

@section('content')
@php
    use App\Models\Usuario;

    // Resolver usuario actual (igual que en tu layout)
    $user = null;
    if (function_exists('auth') && auth()->check()) {
        $user = auth()->user();
    } elseif (session()->has('usuario_id')) {
        $user = Usuario::find(session('usuario_id'));
    }

    $isAdmin = $user && $user->rol === 'admin';
    $esAutor = $user && $comentario->id_usuario === $user->id_usuario;
@endphp

<div class="max-w-4xl mx-auto space-y-4">

  {{-- Barra superior con botón volver --}}
  <div class="flex items-center justify-between">
      <h1 class="text-lg font-semibold text-gray-900">Detalle del Comentario</h1>

      <a href="{{ $isAdmin ? route('admin.comentarios.index') : route('comentarios.index') }}"
         class="inline-flex items-center px-3 py-1.5 text-xs rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
          ← Volver a comentarios
      </a>
  </div>

  {{-- Card principal --}}
  <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-start justify-between gap-3">
      <div>
        <h2 class="text-xl font-semibold text-gray-900">{{ $comentario->titulo ?? 'Sin título' }}</h2>
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

    {{-- Respuestas --}}
    <div class="space-y-3">
      <h3 class="text-sm font-semibold text-gray-700">Respuestas</h3>

      @forelse($comentario->respuestas as $r)
        <div class="border rounded-lg px-4 py-3 bg-white">
          <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span>{{ $r->autor?->nombre_completo ?? 'Usuario' }}</span>
            <span>{{ $r->created_at->format('d/m/Y H:i') }}</span>
          </div>
          <p class="text-sm text-gray-800 whitespace-pre-line">{{ $r->mensaje }}</p>

          @if($isAdmin)
            <form action="{{ route('admin.respuestas.destroy',$r->id_respuesta) }}"
                  method="POST"
                  class="mt-2 text-right"
                  onsubmit="return confirm('¿Eliminar respuesta?');">
              @csrf @method('DELETE')
              <button class="text-xs text-red-600 hover:underline">Eliminar</button>
            </form>
          @endif
        </div>
      @empty
        <p class="text-sm text-gray-500">Aún no hay respuestas.</p>
      @endforelse
    </div>

    {{-- Formulario para responder (ADMIN o AUTOR, solo si está abierto) --}}
    @if($comentario->estado === 'abierto' && ($isAdmin || $esAutor))
      <form action="{{ route('admin.comentarios.respuestas.store',$comentario->id_comentario) }}"
            method="POST" class="space-y-3 mt-4">
        @csrf
        <label class="text-sm text-gray-700">Responder</label>
        <textarea name="mensaje" rows="4"
                  class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('mensaje') }}</textarea>
        @error('mensaje')
          <p class="text-red-600 text-xs">{{ $message }}</p>
        @enderror
        <div class="flex justify-end">
          <button class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            Enviar respuesta
          </button>
        </div>
      </form>
    @endif

    {{-- Solo ADMIN: cerrar/reabrir hilo --}}
    @if($isAdmin)
      <form action="{{ route('admin.comentarios.estado',$comentario->id_comentario) }}"
            method="POST" class="text-right mt-3">
        @csrf @method('PATCH')
        <input type="hidden" name="estado" value="{{ $comentario->estado==='abierto' ? 'cerrado' : 'abierto' }}">
        <button class="mt-2 px-3 py-2 border rounded hover:bg-gray-50 text-sm">
          {{ $comentario->estado==='abierto' ? 'Cerrar hilo' : 'Reabrir hilo' }}
        </button>
      </form>
    @endif

    @include('components.flash')

  </div>
</div>
@endsection
