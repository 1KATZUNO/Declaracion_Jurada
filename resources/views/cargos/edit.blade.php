@extends('layout')
@section('titulo', 'Editar Cargo')
@section('contenido')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Editar Cargo</h2>
            <p class="text-blue-100 text-sm mt-1">Modifique la información del cargo</p>
        </div>

        <form method="POST" action="{{ route('cargos.update', $cargo->id_cargo) }}" class="p-8">
            @csrf @method('PUT')
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" name="nombre" value="{{ $cargo->nombre }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jornada</label>
                    <input type="text" name="jornada" value="{{ $cargo->jornada }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">{{ $cargo->descripcion }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('cargos.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                   Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
