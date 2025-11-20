@extends('layout')

@section('content')
<div class="container mx-auto w-full max-w-6xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Registrar nuevo usuario</h2>
            <p class="text-blue-50 text-sm mt-1">Complete los datos del nuevo usuario</p>
        </div>

        <form action="{{ route('usuarios.store') }}" method="POST" class="p-2 sm:p-4 md:p-8">
            @csrf

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información personal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                        @error('nombre')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido</label>
                        <input type="text" name="apellido" value="{{ old('apellido') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                        @error('apellido')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Identificación</label>
                        <input type="text" name="identificacion" value="{{ old('identificacion') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                        @error('identificacion')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                        @error('telefono')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Credenciales de acceso</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico</label>
                        <input type="email" name="correo" value="{{ old('correo') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                        @error('correo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                        <select name="rol" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                            <option value="funcionario" {{ old('rol')==='funcionario'?'selected':'' }}>Funcionario</option>
                            <option value="admin" {{ old('rol')==='admin'?'selected':'' }}>Administrador</option>
                        </select>
                        @error('rol')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('usuarios.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                   Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                    Guardar usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
