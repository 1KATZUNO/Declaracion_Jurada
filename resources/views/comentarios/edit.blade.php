@extends('layout')

@section('titulo','Editar Comentario')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold text-gray-900">Editar Comentario</h1>
    <a href="{{ route('comentarios.show',$comentario->id_comentario) }}" class="text-xs px-3 py-1.5 rounded border hover:bg-gray-50">Volver</a>
  </div>

  @if($comentario->estado !== 'abierto')
    <div class="mb-4 text-sm bg-gray-100 text-gray-700 px-3 py-2 rounded">
      Este hilo está cerrado; no se puede editar.
    </div>
  @endif

  <form method="POST" action="{{ route('comentarios.update',$comentario->id_comentario) }}" class="space-y-4">
    @csrf @method('PUT')

    <div>
      <label class="text-sm text-gray-700">Título (opcional)</label>
      <input type="text"
             name="titulo"
             value="{{ old('titulo',$comentario->titulo) }}"
             maxlength="200"
             class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
             {{ $comentario->estado!=='abierto' ? 'disabled' : '' }}>
      @error('titulo')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="text-sm text-gray-700">Mensaje</label>
      <textarea name="mensaje"
                rows="6"
                maxlength="10000"
                class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                {{ $comentario->estado!=='abierto' ? 'disabled' : '' }}
                oninput="this.nextElementSibling.querySelector('b').textContent=(10000-this.value.length)">{{ old('mensaje',$comentario->mensaje) }}</textarea>
      <p class="text-[11px] text-gray-500 mt-1">Quedan
        <b>{{ 10000 - strlen(old('mensaje',$comentario->mensaje ?? '')) }}</b> caracteres.</p>
      @error('mensaje')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end gap-2">
      <a href="{{ route('comentarios.show',$comentario->id_comentario) }}" class="px-3 py-2 border rounded hover:bg-gray-50">Cancelar</a>
      <button type="submit"
              class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
              {{ $comentario->estado!=='abierto' ? 'disabled' : '' }}>
        Guardar
      </button>
    </div>
  </form>
</div>
@endsection
