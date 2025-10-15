@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Registrar nuevo usuario</h2>

    <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="nombre" class="block text-sm font-semibold text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                       class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: María">
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="apellido" class="block text-sm font-semibold text-gray-700">Apellido</label>
                <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}"
                       class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Rodríguez">
                @error('apellido')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="correo" class="block text-sm font-semibold text-gray-700">Correo electrónico</label>
                <input type="email" name="correo" id="correo" value="{{ old('correo') }}"
                       class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: usuario@ucr.ac.cr">
                @error('correo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="telefono" class="block text-sm font-semibold text-gray-700">Teléfono</label>
                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                       class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: 8888-9999">
                @error('telefono')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="contrasena" class="block text-sm font-semibold text-gray-700">Contraseña</label>
            <input type="password" name="contrasena" id="contrasena"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500"
                   placeholder="••••••••">
            @error('contrasena')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="rol" class="block text-sm font-semibold text-gray-700">Rol del usuario</label>
            <select name="rol" id="rol"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
                <option value="">Seleccione un rol</option>
                <option value="funcionario" {{ old('rol') === 'funcionario' ? 'selected' : '' }}>Funcionario</option>
                <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
            </select>
            @error('rol')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end mt-6 space-x-4">
            <a href="{{ route('usuarios.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow-md transition">
               Cancelar
            </a>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md transition">
                Guardar usuario
            </button>
        </div>
    </form>
</div>
@endsection
