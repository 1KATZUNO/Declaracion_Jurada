@extends('layout')

@section('titulo', 'Panel Principal')

@section('contenido')
<div class="bg-white shadow-xl rounded-xl p-8 text-center">
  <h1 class="text-3xl font-bold text-indigo-700 mb-3">Bienvenido al Sistema de Declaraciones Juradas</h1>
  <p class="text-gray-700 mb-6">Aquí podrás gestionar usuarios, declaraciones, cargos, unidades académicas y más.</p>
  <div class="flex justify-center gap-4 flex-wrap">
    <x-button href="{{ route('usuarios.index') }}" color="blue">👤 Usuarios</x-button>
    <x-button href="{{ route('declaraciones.index') }}" color="indigo">📜 Declaraciones</x-button>
    <x-button href="{{ route('documentos.index') }}" color="green">📂 Documentos</x-button>
    <x-button href="{{ route('notificaciones.index') }}" color="yellow">🔔 Notificaciones</x-button>
  </div>
</div>
@endsection
