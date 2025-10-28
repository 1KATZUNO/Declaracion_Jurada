<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('titulo', 'Declaraciones UCR')</title>

  {{-- Tipografía --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      /* Colores institucionales y grises */
      --ucr-azul:#0B2C59;
      --ucr-azul-menu:#0F2B55;
      --ucr-fondo:#F3F5F7;
      --ucr-top-gray:#BDBDBD;
      --ucr-top-border:#C7CCD3;

      /* Medidas base */
      --container-max:1440px;
      --topbar-h:110px;      
      --sidebar-w:240px;
      --content-px:36px;
      --content-py:28px;
      --logo-size:44px;
      --user-avatar:28px;
    }
    html,body{
      font-family:'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans';
      background:var(--ucr-fondo);
      color:#0f172a;
    }
    .nav-item{
      display:flex; align-items:center; gap:.75rem;
      padding:.75rem 1rem;
      font-size:.9rem;
      color:#0f172a;
      transition:background .15s ease;
    }
    .nav-item:hover{
      background:#e5e7eb;
    }
  </style>

  @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen">
  {{-- TOPBAR --}}
  <header class="w-full bg-[var(--ucr-top-gray)] border-b" style="border-color:var(--ucr-top-border)">
    <div class="mx-auto max-w-[var(--container-max)] px-8 flex items-center justify-between" style="height:var(--topbar-h);">
      <div class="flex items-center gap-3">
        {{-- Logo --}}
        <div class="rounded bg-[var(--ucr-azul)] flex items-center justify-center text-white font-semibold"
             style="height:var(--logo-size); width:var(--logo-size); font-size:12px;">
          UCR
        </div>
        <div class="leading-tight">
          <p class="text-[15px] font-semibold tracking-wide text-[var(--ucr-azul)]">DECLARACIONES</p>
          <p class="text-[10px] text-gray-700 -mt-0.5">JURADAS DE HORARIO</p>
        </div>
      </div>

      {{-- Navegación superior --}}
      <nav class="hidden lg:flex items-center gap-8 text-[13px] font-medium text-gray-800">
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 inline">
          @csrf
          <button type="submit" class="hover:text-[var(--ucr-azul)] bg-transparent border-0 p-0 m-0 cursor-pointer">
            Cerrar sesión
          </button>
        </form>
        <a href="#" class="hover:text-[var(--ucr-azul)]">ACCESIBILIDAD</a>
        <a href="#" class="hover:text-[var(--ucr-azul)]">AYUDA</a>
        <a href="#" class="hover:text-[var(--ucr-azul)]">ACERCA DE</a>
      </nav>

      {{-- Usuario logueado (clickable) --}}
      @php
        $nombreActual = session('usuario_nombre');
        if (!$nombreActual && function_exists('auth') && auth()->check()) {
          $u = auth()->user();
          $nombreActual = trim(($u->nombre ?? '').' '.($u->apellido ?? '')) ?: ($u->name ?? 'Usuario');
        }
        if (!$nombreActual && session()->has('usuario')) {
          $su = session('usuario');
          if (is_array($su)) {
            $nombreActual = trim(($su['nombre'] ?? '').' '.($su['apellido'] ?? '')) ?: ($su['name'] ?? 'Usuario');
          }
        }
        if (!$nombreActual) $nombreActual = 'Usuario';

        // avatar: preferir session, luego auth user->avatar si existe
        $avatarUrl = session('usuario_avatar') ?? (function_exists('auth') && auth()->check() ? (auth()->user()->avatar ?? null) : null);
        // rol
        $rolActual = session('usuario_rol') ?? (function_exists('auth') && auth()->check() ? (auth()->user()->rol ?? null) : null);
      @endphp

      <div class="relative">
        <button id="user-button" class="flex items-center gap-2 bg-white border border-gray-300 rounded-full px-3 py-1.5 focus:outline-none">
          <div class="rounded-full bg-gray-300 overflow-hidden"
               style="height:var(--user-avatar); width:var(--user-avatar);">
            @if($avatarUrl)
              <img src="{{ asset($avatarUrl) }}" alt="avatar" class="w-full h-full object-cover">
            @else
              <span class="block w-full h-full grid place-content-center">👤</span>
            @endif
          </div>
          <span class="text-[12px] text-gray-800">{{ $nombreActual }}</span>
          <svg class="w-3 h-3 text-gray-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 011.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </button>

        <!-- Dropdown perfil -->
        <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
          <div class="p-4">
            <p class="text-sm font-semibold">{{ $nombreActual }}</p>
            <p class="text-xs text-gray-500 mb-3">{{ $rolActual ? strtoupper($rolActual) : 'ROL DESCONOCIDO' }}</p>

            <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
              @csrf
              <div>
                <label class="block text-xs text-gray-600 mb-1">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', (function_exists('auth') && auth()->check()) ? auth()->user()->nombre : '') }}"
                       class="w-full px-3 py-2 border rounded text-sm">
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Apellido</label>
                <input type="text" name="apellido" value="{{ old('apellido', (function_exists('auth') && auth()->check()) ? auth()->user()->apellido : '') }}"
                       class="w-full px-3 py-2 border rounded text-sm">
              </div>
              <div>
                <label class="block text-xs text-gray-600 mb-1">Foto de perfil</label>
                <input type="file" name="avatar" id="avatar-input" accept="image/*" class="text-xs">
                <div id="avatar-preview" class="mt-2 w-20 h-20 rounded overflow-hidden border" style="display:{{ $avatarUrl ? 'block' : 'none' }};">
                  @if($avatarUrl)<img src="{{ asset($avatarUrl) }}" id="avatar-preview-img" class="w-full h-full object-cover">@endif
                </div>
              </div>

              <div class="flex items-center justify-between">
                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded">Guardar</button>
                <a href="{{ route('declaraciones.index') }}" class="text-xs text-gray-500">Mi perfil</a>
              </div>
            </form>
          </div>
        </div>
      </div>
      {{-- fin usuario --}}
    </div>
  </header>

  {{-- DETECCIÓN SI HAY QUE OCULTAR SIDEBAR --}}
  @php $hideSidebar = View::hasSection('hide_sidebar'); @endphp

  {{-- CONTENEDOR PRINCIPAL --}}
  <div class="w-full bg-[var(--ucr-top-gray)]">
    <div class="mx-auto w-full max-w-[var(--container-max)]" 
         style="display:grid; grid-template-columns: {{ $hideSidebar ? '1fr' : 'var(--sidebar-w) 1fr' }};">
      
      {{-- SIDEBAR  --}}
      @unless($hideSidebar)
        <aside class="min-h-[calc(100vh-var(--topbar-h))] bg-[var(--ucr-top-gray)] border-r border-gray-300">
          <div class="bg-[var(--ucr-azul-menu)] text-white px-4 py-2.5 text-[13px] font-semibold uppercase">
            Menú Principal
          </div>

          <nav class="pt-1.5">
            <a href="{{ route('unidades.index') }}" class="nav-item"><span>🏫</span><span class="font-medium">Mis Unidades Académicas</span></a>
            <a href="{{ route('sedes.index') }}" class="nav-item"><span>📍</span><span class="font-medium">Sedes</span></a>

            @if (Route::has('usuarios.index'))
              <a href="{{ route('usuarios.index') }}" class="nav-item"><span>👤</span><span class="font-medium">Usuarios</span></a>
            @endif
            @if (Route::has('cargos.index'))
              <a href="{{ route('cargos.index') }}" class="nav-item"><span>🧩</span><span class="font-medium">Cargos</span></a>
            @endif
            @if (Route::has('formularios.index'))
              <a href="{{ route('formularios.index') }}" class="nav-item"><span>📄</span><span class="font-medium">Formularios</span></a>
            @endif
            @if (Route::has('declaraciones.index'))
              <a href="{{ route('declaraciones.index') }}" class="nav-item"><span>📝</span><span class="font-medium">Declaraciones</span></a>
            @endif
            @if (Route::has('horarios.index'))
              <a href="{{ route('horarios.index') }}" class="nav-item"><span>🕒</span><span class="font-medium">Horarios</span></a>
            @endif
            @if (Route::has('documentos.index'))
              <a href="{{ route('documentos.index') }}" class="nav-item"><span>📎</span><span class="font-medium">Documentos</span></a>
            @endif
            @if (Route::has('notificaciones.index'))
              <a href="{{ route('notificaciones.index') }}" class="nav-item"><span>🔔</span><span class="font-medium">Notificaciones</span></a>
            @endif
          </nav>
        </aside>
      @endunless

      {{-- CONTENIDO PRINCIPAL --}}
      <main class="bg-[var(--ucr-fondo)]" style="padding:var(--content-py) var(--content-px);">
        @includeIf('components.flash')

        @hasSection('content')
          @yield('content')
        @elseif (View::hasSection('contenido'))
          @yield('contenido')
        @endif
      </main>
    </div>
  </div>

  {{-- FOOTER --}}
  <footer class="bg-[var(--ucr-azul)] text-blue-100 text-center text-[11px] py-3">
    © {{ date('Y') }} Universidad de Costa Rica — Sistema de Gestión Académica
  </footer>

  <script>
    // toggle dropdown
    document.addEventListener('click', function(e){
      const btn = document.getElementById('user-button');
      const dd = document.getElementById('user-dropdown');
      if (!btn || !dd) return;
      if (btn.contains(e.target)) {
        dd.classList.toggle('hidden');
      } else if (!dd.contains(e.target)) {
        dd.classList.add('hidden');
      }
    });

    // preview avatar
    const avatarInput = document.getElementById('avatar-input');
    if (avatarInput) {
      avatarInput.addEventListener('change', function(){
        const file = this.files && this.files[0];
        const preview = document.getElementById('avatar-preview');
        const previewImg = document.getElementById('avatar-preview-img');
        if (!file) {
          if (preview) preview.style.display = 'none';
          return;
        }
        const url = URL.createObjectURL(file);
        if (previewImg) {
          previewImg.src = url;
        } else if (preview) {
          const img = document.createElement('img');
          img.id = 'avatar-preview-img';
          img.className = 'w-full h-full object-cover';
          img.src = url;
          preview.appendChild(img);
        }
        if (preview) preview.style.display = 'block';
      });
    }
  </script>
</body>
</html>
