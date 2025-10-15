@extends('layout')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Registrar nuevo usuario</h2>

    <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="nombre" class="block text-sm font-semibold text-gray-700">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label for="apellido" class="block text-sm font-semibold text-gray-700">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label for="identificacion" class="block text-sm font-semibold text-gray-700">Identificación</label>
            <input type="text" id="identificacion" name="identificacion" value="{{ old('identificacion') }}"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label for="correo" class="block text-sm font-semibold text-gray-700">Correo electrónico</label>
            <input type="email" id="correo" name="correo" value="{{ old('correo') }}"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label for="telefono" class="block text-sm font-semibold text-gray-700">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="rol" class="block text-sm font-semibold text-gray-700">Rol</label>
            <select id="rol" name="rol"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="funcionario">Funcionario</option>
                <option value="admin">Administrador</option>
            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md transition">
                Guardar usuario
            </button>
        </div>
    </form>
</div>
@endsection
