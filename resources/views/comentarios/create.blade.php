@extends('layout')

@section('titulo','Nuevo Comentario')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-3xl mx-auto">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold text-gray-900">Nuevo Comentario</h1>
    <a href="{{ route('comentarios.index') }}" class="text-xs px-3 py-1.5 rounded border hover:bg-gray-50">
      Volver
    </a>
  </div>

  {{-- Alertas --}}
  @if(session('ok'))
    <div class="mb-3 text-sm bg-green-100 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div>
  @endif

  <form method="POST" action="{{ route('comentarios.store') }}" class="space-y-4">
    @csrf

    <div>
      <label class="text-sm text-gray-700">Título (opcional)</label>
      <input type="text"
             name="titulo"
             value="{{ old('titulo') }}"
             maxlength="200"
             class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
      @error('titulo')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="text-sm text-gray-700">Mensaje</label>
      <textarea name="mensaje"
                rows="6"
                maxlength="10000"
                class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                oninput="this.nextElementSibling.querySelector('b').textContent=(10000-this.value.length)">
        {{ old('mensaje') }}</textarea>
      <p class="text-[11px] text-gray-500 mt-1">Quedan <b>{{ 10000 - strlen(old('mensaje','')) }}</b> caracteres.</p>
      @error('mensaje')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end gap-2">
      <a href="{{ route('comentarios.index') }}" class="px-3 py-2 border rounded hover:bg-gray-50">Cancelar</a>
      <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Enviar</button>
    </div>
  </form>
</div>
@endsection
