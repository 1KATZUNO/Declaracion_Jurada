<!DOCTYPE html>
<html lang="es">
   @csrf
<head>
  <meta charset="UTF-8">
  <title>@yield('titulo','DJ UCR')</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-900">
  <nav class="bg-blue-700 text-white p-4">
    <div class="container mx-auto flex gap-4">
      <a href="{{ route('home') }}" class="font-bold">DJ UCR</a>
      <a href="{{ route('usuarios.index') }}">Usuarios</a>
      <a href="{{ route('sedes.index') }}">Sedes</a>
      <a href="{{ route('unidades.index') }}">Unidades</a>
      <a href="{{ route('cargos.index') }}">Cargos</a>
      <a href="{{ route('formularios.index') }}">Formularios</a>
      <a href="{{ route('declaraciones.index') }}">Declaraciones</a>
      <a href="{{ route('horarios.index') }}">Horarios</a>
      <a href="{{ route('documentos.index') }}">Documentos</a>
      <a href="{{ route('notificaciones.index') }}">Notificaciones</a>
    </div>
  </nav>
  <main class="container mx-auto p-6">
    @if(session('ok'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('ok') }}</div>
    @endif
    @yield('contenido')
  </main>
</body>
</html>
