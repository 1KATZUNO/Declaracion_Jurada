@extends('layout')

@section('titulo', 'Editar Usuario')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6 max-w-2xl mx-auto">
  <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Editar Usuario</h2>

  <form method="POST" action="{{ route('usuarios.update', $usuario->id_usuario) }}" class="space-y-4">
    @csrf @method('PUT')

    <div>
      <label class="block text-gray-700 font-semibold">Nombre</label>
      <input type="text" name="nombre" value="{{ $usuario->nombre }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
    </div>

    <div>
      <label class="block text-gray-700 font-semibold">Apellido</label>
      <input type="text" name="apellido" value="{{ $usuario->apellido }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
    </div>

    <div>
      <label class="block text-gray-700 font-semibold">Correo</label>
      <input type="email" name="correo" value="{{ $usuario->correo }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
    </div>

    <div>
      <label class="block text-gray-700 font-semibold">Nueva Contraseña (opcional)</label>
      <input type="password" name="contrasena" placeholder="••••••••" class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div>
      <label class="block text-gray-700 font-semibold">Teléfono</label>
      <input type="text" name="telefono" value="{{ $usuario->telefono }}" class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div>
      <label class="block text-gray-700 font-semibold">Rol</label>
      <select name="rol" class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
        <option value="funcionario" {{ $usuario->rol === 'funcionario' ? 'selected' : '' }}>Funcionario</option>
        <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>Admin</option>
      </select>
    </div>

    <div class="pt-4 flex justify-end gap-2">
      <x-button href="{{ route('usuarios.index') }}" color="red">Cancelar</x-button>
      <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold">Actualizar</button>
    </div>
  </form>
</div>
@endsection
