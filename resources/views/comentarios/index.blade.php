@extends('layout')

@section('titulo','Mis Comentarios')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-gray-900">Mis Comentarios</h1>

        <div class="flex items-center gap-2">
            {{-- Botón Actualizar --}}
            <a href="{{ route('comentarios.index') }}"
               class="px-3 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 text-sm flex items-center gap-1">
                Actualizar
            </a>

            {{-- Botón Nuevo --}}
            <a href="{{ route('comentarios.create') }}"
               class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Nuevo
            </a>
        </div>
    </div>

    {{-- MENSAJE DE ÉXITO --}}
    @if(session('ok'))
        <div class="mb-3 text-sm bg-green-100 text-green-800 px-3 py-2 rounded">
            {{ session('ok') }}
        </div>
    @endif

    {{-- TABLA --}}
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
                        {{-- Título + preview --}}
                        <td class="px-3 py-2 align-top">
                            <div class="font-medium text-gray-900">
                                {{ $c->titulo ?? 'Sin título' }}
                            </div>
                            <div class="text-[12px] text-gray-500">
                                {{ \Illuminate\Support\Str::limit($c->mensaje ?? '', 90) }}
                            </div>
                        </td>

                        {{-- Estado --}}
                        <td class="px-3 py-2 align-top">
                            <span class="px-2 py-0.5 text-[10px] rounded-full
                                {{ $c->estado === 'abierto'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-200 text-gray-700' }}">
                                {{ ucfirst($c->estado) }}
                            </span>
                        </td>

                        {{-- Fecha --}}
                        <td class="px-3 py-2 align-top">
                            <div>{{ $c->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-[10px] text-gray-400">
                                {{ $c->created_at->diffForHumans() }}
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-3 py-2 text-center align-top">
                            <a class="text-blue-600 text-xs hover:underline"
                               href="{{ route('comentarios.show',$c->id_comentario) }}">
                               Ver
                            </a>

                            {{-- Solo si está abierto --}}
                            @if($c->estado === 'abierto')
                                <a class="text-xs text-amber-600 ml-2 hover:underline"
                                   href="{{ route('comentarios.edit',$c->id_comentario) }}">
                                   Editar
                                </a>

                                {{-- Botón que abre el modal bonito --}}
                                <button type="button"
                                        class="text-xs text-red-600 ml-2 hover:underline"
                                        data-action="{{ route('comentarios.destroy',$c->id_comentario) }}"
                                        onclick="openDeleteComentarioModal(this)">
                                    Eliminar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-gray-500">
                            Sin registros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-4">
        {{ $comentarios->links() }}
    </div>

</div>

{{-- MODAL BONITO PARA ELIMINAR --}}
<div id="modal-eliminar-comentario"
     class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full mx-4 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">
            Eliminar comentario
        </h2>
        <p class="text-sm text-gray-600 mb-4">
            ¿Estás seguro de que deseas eliminar este comentario?  
            Esta acción no se puede deshacer.
        </p>

        <form id="form-eliminar-comentario" method="POST" action="#">
            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                        onclick="closeDeleteComentarioModal()"
                        class="px-3 py-2 text-sm rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-3 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700">
                    Eliminar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT DEL MODAL --}}
<script>
    function openDeleteComentarioModal(button) {
        const modal = document.getElementById('modal-eliminar-comentario');
        const form  = document.getElementById('form-eliminar-comentario');

        // Asignar la acción del form según el comentario
        form.action = button.dataset.action;

        modal.classList.remove('hidden');
    }

    function closeDeleteComentarioModal() {
        const modal = document.getElementById('modal-eliminar-comentario');
        modal.classList.add('hidden');
    }
</script>
@endsection
