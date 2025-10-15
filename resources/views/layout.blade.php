<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('titulo', 'Declaraciones Juradas UCR')</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-900">
  <nav class="bg-indigo-700 text-white shadow-md">
    <div class="container mx-auto flex justify-between items-center p-4">
      <a href="{{ route('home') }}" class="font-bold text-lg">Declaraciones Juradas</a>
      <div class="space-x-4">
        <a href="{{ route('usuarios.index') }}" class="hover:text-gray-200">Usuarios</a>
        <a href="{{ route('sedes.index') }}" class="hover:text-gray-200">Sedes</a>
        <a href="{{ route('unidades.index') }}" class="hover:text-gray-200">Unidades</a>
        <a href="{{ route('cargos.index') }}" class="hover:text-gray-200">Cargos</a>
        <a href="{{ route('formularios.index') }}" class="hover:text-gray-200">Formularios</a>
        <a href="{{ route('declaraciones.index') }}" class="hover:text-gray-200">Declaraciones</a>
        <a href="{{ route('documentos.index') }}" class="hover:text-gray-200">Documentos</a>
        <a href="{{ route('notificaciones.index') }}" class="hover:text-gray-200">Notificaciones</a>
      </div>
    </div>
  </nav>

  <main class="container mx-auto mt-6 px-4">
    @if(session('ok'))
      <x-alert tipo="success" :mensaje="session('ok')" />
    @endif

    @yield('contenido')
  </main>

  <footer class="mt-10 text-center text-gray-500 py-4 border-t">
    <p class="text-sm">© {{ date('Y') }} Universidad de Costa Rica — Declaraciones Juradas</p>
  </footer>
</body>
</html>
