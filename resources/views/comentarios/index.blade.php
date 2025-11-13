@extends('layout')

@section('titulo','Mis Comentarios')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold text-gray-900">Mis Comentarios</h1>
    <a href="{{ route('comentarios.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nuevo</a>
  </div>

  @if(session('ok'))
    <div class="mb-3 text-sm bg-green-100 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div>
  @endif

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-3 py-2 text-left">Título</th>
          <th class="px-3 py-2 text-left">Estado</th>
          <th class="px-3 py-2 text-left">Creado</th>
          <th class="px-3 py-2 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($comentarios as $c)
          <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }} border-b border-gray-100">
            <td class="px-3 py-2 align-top">
              <div class="font-medium text-gray-900">{{ $c->titulo ?? 'Sin título' }}</div>
              <div class="text-[12px] text-gray-500">{{ \Illuminate\Support\Str::limit($c->mensaje ?? '', 90) }}</div>
            </td>
            <td class="px-3 py-2 align-top">
              <span class="px-2 py-0.5 text-[10px] rounded-full {{ $c->estado==='abierto'?'bg-green-100 text-green-700':'bg-gray-200 text-gray-700' }}">
                {{ ucfirst($c->estado) }}
              </span>
            </td>
            <td class="px-3 py-2 align-top">
              <div>{{ $c->created_at->format('d/m/Y H:i') }}</div>
              <div class="text-[10px] text-gray-400">{{ $c->created_at->diffForHumans() }}</div>
            </td>
            <td class="px-3 py-2 text-center align-top">
              <a class="text-blue-600 text-xs hover:underline" href="{{ route('comentarios.show',$c->id_comentario) }}">Ver</a>

              @if($c->estado==='abierto')
                <a class="text-xs text-amber-600 ml-2 hover:underline" href="{{ route('comentarios.edit',$c->id_comentario) }}">Editar</a>

                <form action="{{ route('comentarios.destroy',$c->id_comentario) }}"
                      method="POST" class="inline"
                      onsubmit="return confirm('¿Eliminar este comentario?');">
                  @csrf @method('DELETE')
                  <button class="text-xs text-red-600 ml-2 hover:underline" type="submit">Eliminar</button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-3 py-8 text-center text-gray-500">Sin registros.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $comentarios->links() }}
  </div>
</div>
@endsection
