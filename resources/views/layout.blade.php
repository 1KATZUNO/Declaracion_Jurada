<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('titulo', 'Declaraciones UCR')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
  <header class="bg-indigo-700 text-white">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <a href="{{ route('home') }}" class="font-bold text-xl">Declaraciones UCR</a>
      <nav class="space-x-3 text-sm">
        <a href="{{ route('usuarios.index') }}" class="hover:underline">Usuarios</a>
        <a href="{{ route('sedes.index') }}" class="hover:underline">Sedes</a>
        <a href="{{ route('unidades.index') }}" class="hover:underline">Unidades</a>
        <a href="{{ route('cargos.index') }}" class="hover:underline">Cargos</a>
        <a href="{{ route('formularios.index') }}" class="hover:underline">Formularios</a>
        <a href="{{ route('declaraciones.index') }}" class="hover:underline">Declaraciones</a>
        <a href="{{ route('horarios.index') }}" class="hover:underline">Horarios</a>
        <a href="{{ route('documentos.index') }}" class="hover:underline">Documentos</a>
        <a href="{{ route('notificaciones.index') }}" class="hover:underline">Notificaciones</a>
      </nav>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-6 py-8">
    {{-- Soportar vistas antiguas y nuevas --}}
    @hasSection('content')
      @yield('content')
    @elseif (View::hasSection('contenido'))
      @yield('contenido')
    @endif
  </main>

  <footer class="text-center text-xs text-gray-500 py-6">
    &copy; {{ date('Y') }} UCR — Declaraciones Juradas
  </footer>
</body>
</html>
