<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('titulo', 'Declaraciones UCR')</title>
  @vite(['resources/css/app.css'])
  {{-- @vite(['resources/js/app.js']) --}}
</head>
<body class="bg-gray-50 min-h-screen">
  <header class="bg-gradient-to-r from-blue-600 to-blue-700 shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <a href="{{ route('home') }}" class="font-semibold text-xl text-white hover:text-blue-100 transition-colors">
        Declaraciones UCR
      </a>
      <nav class="flex gap-1">
        <a href="{{ route('usuarios.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Usuarios
        </a>
        <a href="{{ route('sedes.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Sedes
        </a>
        <a href="{{ route('unidades.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Unidades
        </a>
        <a href="{{ route('cargos.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Cargos
        </a>
        <a href="{{ route('formularios.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Formularios
        </a>
        <a href="{{ route('declaraciones.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Declaraciones
        </a>
        <a href="{{ route('horarios.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Horarios
        </a>
        <a href="{{ route('documentos.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Documentos
        </a>
        <a href="{{ route('notificaciones.index') }}" class="px-3 py-2 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-600/50 rounded-md transition-colors">
          Notificaciones
        </a>
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

  <footer class="text-center text-sm text-gray-500 py-6 border-t border-gray-200">
    &copy; {{ date('Y') }} UCR — Sistema de Declaraciones Juradas
  </footer>
</body>
</html>
