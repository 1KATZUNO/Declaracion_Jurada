@extends('layout')
@section('titulo', 'Nuevo Cargo')
@section('contenido')
 @csrf
<div class="container mx-auto w-full max-w-4xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Registrar Cargo</h2>
            <p class="text-blue-100 text-sm mt-1">Complete la información del nuevo cargo</p>
        </div>

        <form method="POST" action="{{ route('cargos.store') }}" class="p-2 sm:p-4 md:p-8">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" name="nombre"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                </div>

                {{-- Eliminado campo "Jornada" (se gestiona en módulo Jornadas) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white"></textarea>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('cargos.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                   Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
