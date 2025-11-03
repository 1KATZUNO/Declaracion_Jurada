@extends('layout')
@section('titulo', 'Editar Formulario')
@section('contenido')
 @csrf
<div class="container mx-auto w-full max-w-4xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Editar Formulario</h1>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('formularios.update', $formulario->id_formulario) }}" class="p-2 sm:p-4 md:p-6">
            @csrf 
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Título -->
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" id="titulo" name="titulo" value="{{ $formulario->titulo }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $formulario->descripcion }}</textarea>
                </div>

                <!-- Fecha de Creación -->
                <div>
                    <label for="fecha_creacion" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Creación</label>
                    <input type="date" id="fecha_creacion" name="fecha_creacion" value="{{ $formulario->fecha_creacion }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex flex-col md:flex-row items-center justify-end gap-3">
                <a href="{{ route('formularios.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
