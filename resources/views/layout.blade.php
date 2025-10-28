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

  /* Topbar responsive tweaks */
  .topbar-container{
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    padding-left:1rem;
    padding-right:1rem;
    gap:0.5rem;
  }
  @media(min-width:768px){
    .topbar-container{ padding-left:2rem; padding-right:2rem; }
  }

  /* Shift logo left — corregido */
  .logo-shift{ margin-left:-60px; } /* antes -20px */

  @media(max-width:640px){ 
    .logo-shift{ margin-left:-40px; } 
  }

  /* Mostrar enlaces auxiliares solo en md+ */
  .aux-nav{ display:none; }
  @media(min-width:768px){ .aux-nav{ display:block; } }
</style>

  @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen">
  {{-- TOPBAR --}}
  <header class="w-full" style="background:#0369a1;">
    <div class="mx-auto max-w-[var(--container-max)] topbar-container" style="height:var(--topbar-h);">
      <div class="flex items-center gap-3">
        {{-- Logo Universidad + imagen de encabezado (firma) --}}
        <div class="flex items-center gap-3 logo-shift">
          <div style="height:var(--logo-size); width:var(--logo-size);">
            <img src="{{ asset('imagenes/uc_logo.png') }}" alt="Universidad de Costa Rica" class="h-full w-full object-contain" onerror="this.onerror=null; this.style.display='none'">
          </div>
          <div class="ml-0">
            <img src="{{ asset('imagenes/firma-horizontal-una-linea-reverso-rgb.png') }}" alt="Firma UCR"
                 class="h-20 object-contain" style="max-height:90px; display:block;" onerror="this.onerror=null; this.style.display='none'">
          </div>
        </div>
      </div>

      {{-- Derecha: cerrar sesión + usuario --}}
      <div class="flex items-center gap-4">
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 inline">
          @csrf
          <button type="submit" class="text-white bg-transparent border-0 p-0 m-0 cursor-pointer text-sm font-medium">Cerrar sesión</button>
        </form>

        @php
          $nombreActual = session('usuario_nombre') ?? (function_exists('auth') && auth()->check() ? trim((auth()->user()->nombre ?? '') . ' ' . (auth()->user()->apellido ?? '')) : 'Usuario');
          $avatarSrc = session('usuario_avatar') ?? null;
          if (empty($avatarSrc) && function_exists('auth') && auth()->check()) {
              $dbAvatar = auth()->user()->avatar ?? null;
              if (!empty($dbAvatar)) {
                  $avatarSrc = \Illuminate\Support\Facades\Storage::url($dbAvatar);
              }
          }
          if (!empty($avatarSrc) && !preg_match('/^(?:https?:)?\\/\\//', $avatarSrc) && strpos($avatarSrc, '/') !== 0) {
              $avatarSrc = asset($avatarSrc);
          }
        @endphp

        <div class="relative">
          <button id="user-button" class="flex items-center gap-2 bg-white/10 text-white border border-white/20 rounded-full px-3 py-1.5 focus:outline-none">
            <div class="rounded-full bg-white/20 overflow-hidden" style="height:var(--user-avatar); width:var(--user-avatar);">
              @if(!empty($avatarSrc))
                <img id="user-avatar-top-img" src="{{ $avatarSrc }}" alt="avatar" class="w-full h-full object-cover">
              @else
                <span id="user-avatar-top-placeholder" class="block w-full h-full grid place-content-center">👤</span>
              @endif
            </div>
            <span class="text-[12px] text-white">{{ $nombreActual }}</span>
            <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 011.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
          </button>

          <!-- Dropdown perfil -->
          <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
            <div class="p-4">
              <p class="text-sm font-semibold text-gray-800">{{ $nombreActual }}</p>
              <p class="text-xs text-gray-500 mb-3">{{ session('usuario_rol') ? strtoupper(session('usuario_rol')) : 'ROL DESCONOCIDO' }}</p>

              <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Nombre</label>
                  <input type="text" name="nombre" value="{{ old('nombre', (function_exists('auth') && auth()->check()) ? auth()->user()->nombre : '') }}" class="w-full px-3 py-2 border rounded text-sm">
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Apellido</label>
                  <input type="text" name="apellido" value="{{ old('apellido', (function_exists('auth') && auth()->check()) ? auth()->user()->apellido : '') }}" class="w-full px-3 py-2 border rounded text-sm">
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Foto de perfil</label>
                  <input type="file" name="avatar" id="avatar-input" accept="image/*" class="text-xs">
                  <div id="avatar-preview" class="mt-2 w-20 h-20 rounded overflow-hidden border" style="display:{{ $avatarSrc ? 'block' : 'none' }};">
                    @if($avatarSrc)<img src="{{ $avatarSrc }}" id="avatar-preview-img" class="w-full h-full object-cover">@endif
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

      </div>
    </div>

    {{-- Enlaces auxiliares (md+) --}}
    <div class="w-full aux-nav" style="background:#0369a1;">
      <div class="mx-auto max-w-[var(--container-max)] px-8 py-2">
        <nav class="flex items-center justify-start gap-6 text-sm text-white/90">
          <a href="#" class="hover:underline">ACCESIBILIDAD</a>
          <a href="#" class="hover:underline">AYUDA</a>
          <a href="#" class="hover:underline">ACERCA DE</a>
        </nav>
      </div>
    </div>
  </header>

  {{-- CONTENIDO PRINCIPAL --}}
  @php $hideSidebar = View::hasSection('hide_sidebar'); @endphp
<div class="w-full bg-[var(--ucr-top-gray)]">
  <div class="mx-auto w-full max-w-[var(--container-max)]" 
       style="display:grid; grid-template-columns: {{ $hideSidebar ? '1fr' : 'var(--sidebar-w) 1fr' }};">
    @unless($hideSidebar)
      <aside class="min-h-[calc(100vh-var(--topbar-h))] bg-[var(--ucr-top-gray)] border-r border-gray-300">
        <div class="bg-[var(--ucr-azul-menu)] text-white px-4 py-2.5 text-[13px] font-semibold uppercase">
          Menú Principal
        </div>

        <nav class="pt-1.5 space-y-0.5">

          @if (Route::has('unidades.index'))
            <a href="{{ route('unidades.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/unidades.png') }}" alt="Unidades" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Mis Unidades Académicas</span>
            </a>
          @endif

          <a href="{{ route('sedes.index') }}" class="nav-item">
            <img src="{{ asset('imagenes/sedes.png') }}" alt="Sedes" class="inline-block w-4 h-4 mr-2">
            <span class="font-medium">Sedes</span>
          </a>

          @if (Route::has('usuarios.index'))
            <a href="{{ route('usuarios.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/usuarios.png') }}" alt="Usuarios" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Usuarios</span>
            </a>
          @endif

          @if (Route::has('cargos.index'))
            <a href="{{ route('cargos.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/cargos.png') }}" alt="Cargos" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Cargos</span>
            </a>
          @endif

          @if (Route::has('formularios.index'))
            <a href="{{ route('formularios.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/formularios.png') }}" alt="Formularios" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Formularios</span>
            </a>
          @endif

          @if (Route::has('declaraciones.index'))
            <a href="{{ route('declaraciones.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/declaraciones.png') }}" alt="Declaraciones" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Declaraciones</span>
            </a>
          @endif

          @if (Route::has('horarios.index'))
            <a href="{{ route('horarios.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/horarios.png') }}" alt="Horarios" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Horarios</span>
            </a>
          @endif

          @if (Route::has('documentos.index'))
            <a href="{{ route('documentos.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/documentos.png') }}" alt="Documentos" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Documentos</span>
            </a>
          @endif

          @if (Route::has('notificaciones.index'))
            <a href="{{ route('notificaciones.index') }}" class="nav-item">
              <img src="{{ asset('imagenes/notificaciones.png') }}" alt="Notificaciones" class="inline-block w-4 h-4 mr-2">
              <span class="font-medium">Notificaciones</span>
            </a>
          @endif

        </nav>
      </aside>
    @endunless


      <main class="bg-[var(--ucr-fondo)]" style="padding:var(--content-py) var(--content-px);">
        @includeIf('components.flash')
        @hasSection('content') @yield('content') @elseif(View::hasSection('contenido')) @yield('contenido') @endif
      </main>
    </div>
  </div>

  {{-- FOOTER --}}
  <footer class="bg-[var(--ucr-azul)] text-blue-100 text-center text-[11px] py-3">© {{ date('Y') }} Universidad de Costa Rica — Sistema de Gestión Académica</footer>

  <script>
    // toggle dropdown
    document.addEventListener('click', function(e){
      const btn = document.getElementById('user-button');
      const dd = document.getElementById('user-dropdown');
      if (!btn || !dd) return;
      if (btn.contains(e.target)) dd.classList.toggle('hidden'); else if (!dd.contains(e.target)) dd.classList.add('hidden');
    });

    // preview avatar + actualizar topbar
    (function(){
      const avatarInput = document.getElementById('avatar-input');
      if (!avatarInput) return;
      avatarInput.addEventListener('change', function(){
        const file = this.files && this.files[0];
        const preview = document.getElementById('avatar-preview');
        let previewImg = document.getElementById('avatar-preview-img');
        const topImg = document.getElementById('user-avatar-top-img');
        const topPlaceholder = document.getElementById('user-avatar-top-placeholder');

        if (!file) { if (preview) preview.style.display = 'none'; return; }
        const url = URL.createObjectURL(file);

        if (previewImg) previewImg.src = url;
        else if (preview) { previewImg = document.createElement('img'); previewImg.id='avatar-preview-img'; previewImg.className='w-full h-full object-cover'; previewImg.src = url; preview.appendChild(previewImg); }
        if (preview) preview.style.display = 'block';

        if (topImg) topImg.src = url;
        else if (topPlaceholder) {
          const img = document.createElement('img'); img.id='user-avatar-top-img'; img.className='w-full h-full object-cover'; img.src = url; topPlaceholder.replaceWith(img);
        }

        const cleanup = () => { try{ URL.revokeObjectURL(url); }catch(e){} };
        if (previewImg) previewImg.onload = previewImg.onerror = cleanup;
      });
    })();
  </script>
</body>
</html>
