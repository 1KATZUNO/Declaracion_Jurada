@extends('layout')
@section('titulo', 'Nueva Notificación')
@section('contenido')
 @csrf
<div class="container mx-auto w-full max-w-4xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Crear Notificación</h1>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('notificaciones.store') }}" class="p-2 sm:p-4 md:p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Usuario -->
                <div>
                    <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                    <select id="id_usuario" name="id_usuario" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="">Seleccione un usuario...</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id_usuario }}">{{ $u->nombre }} {{ $u->apellido }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Mensaje -->
                <div>
                    <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              required></textarea>
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="estado" name="estado" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            required>
                        <option value="pendiente">Pendiente</option>
                        <option value="enviada">Enviada</option>
                        <option value="leída">Leída</option>
                    </select>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex flex-col md:flex-row items-center justify-end gap-3">
                <a href="{{ route('notificaciones.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
