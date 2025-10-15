@extends('layout')

@section('titulo', 'Gestión de Usuarios')

@section('contenido')
<div class="bg-white shadow-lg rounded-xl p-6">
  <div class="flex justify-between items-center mb-5">
    <h2 class="text-2xl font-semibold text-indigo-700">Usuarios Registrados</h2>
    <x-button href="{{ route('usuarios.create') }}" color="blue">➕ Nuevo Usuario</x-button>
  </div>

  <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
    <thead class="bg-indigo-600 text-white">
      <tr>
        <th class="py-2 px-3 text-left">Nombre</th>
        <th class="py-2 px-3 text-left">Correo</th>
        <th class="py-2 px-3 text-left">Teléfono</th>
        <th class="py-2 px-3 text-left">Rol</th>
        <th class="py-2 px-3 text-center">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($usuarios as $u)
      <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
        <td class="py-2 px-3">{{ $u->nombre }} {{ $u->apellido }}</td>
        <td class="py-2 px-3">{{ $u->correo }}</td>
        <td class="py-2 px-3">{{ $u->telefono ?? '—' }}</td>
        <td class="py-2 px-3 capitalize">{{ $u->rol }}</td>
        <td class="py-2 px-3 text-center space-x-2">
          <x-button href="{{ route('usuarios.edit', $u->id_usuario) }}" color="indigo">✏️ Editar</x-button>
          <form action="{{ route('usuarios.destroy', $u->id_usuario) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-semibold"
              onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
              🗑️ Eliminar
            </button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
