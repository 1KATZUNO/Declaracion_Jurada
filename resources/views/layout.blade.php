<!DOCTYPE html>
<html lang="es">
<head>
    @csrf
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
            --container-max:100%;
            --topbar-h:110px;
            --sidebar-w:260px;
            --content-px:1.5rem;
            --content-py:1.5rem;
            --logo-size:44px;
            --user-avatar:28px;
        }

        html,body{
            font-family:'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans';
            background:var(--ucr-fondo);
            color:#0f172a;
        }

        .nav-item{
            display:flex;
            align-items:center;
            gap:.75rem;
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
            .topbar-container{
                padding-left:2rem;
                padding-right:2rem;
            }
        }

        /* Ajuste logo */
        .logo-shift{
            margin-left:-60px;
        }
        @media(max-width:640px){
            .logo-shift{
                margin-left:-40px;
            }
        }

        /* Enlaces auxiliares solo en md+ */
        .aux-nav{
            display:none;
        }
        @media(min-width:768px){
            .aux-nav{
                display:block;
            }
        }
    </style>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen">
@php
    use App\Models\Usuario;
    use Illuminate\Support\Facades\Storage;

    // Resolver usuario actual
    $usuarioActual = null;

    if (function_exists('auth') && auth()->check()) {
        $usuarioActual = auth()->user();
    } elseif (session()->has('usuario_id')) {
        $usuarioActual = Usuario::find(session('usuario_id'));
    }

    // Nombre mostrado
    $nombreActual = session('usuario_nombre')
        ?? ($usuarioActual
            ? trim(($usuarioActual->nombre ?? '') . ' ' . ($usuarioActual->apellido ?? ''))
            : 'Usuario');

    // Avatar
    $avatarSrc = session('usuario_avatar') ?? null;
    if (!$avatarSrc && $usuarioActual && !empty($usuarioActual->avatar ?? null)) {
        $avatarSrc = Storage::url($usuarioActual->avatar);
    }
    if (!empty($avatarSrc)
        && !preg_match('/^(?:https?:)?\/\//', $avatarSrc)
        && strpos($avatarSrc, '/') !== 0) {
        $avatarSrc = asset($avatarSrc);
    }

    // Notificaciones no leídas (Laravel Notifications)
    $unreadNotifications = $usuarioActual
        ? $usuarioActual->unreadNotifications()->take(5)->get()
        : collect();
@endphp

{{-- TOPBAR --}}
<header class="w-full" style="background:#0369a1;">
    <div class="mx-auto max-w-[var(--container-max)] topbar-container" style="height:var(--topbar-h);">
        <div class="flex items-center gap-3">
            {{-- Logo Universidad + firma --}}
            <div class="flex items-center gap-3 logo-shift">
                <div style="height:var(--logo-size); width:var(--logo-size);">
                    <img src="{{ asset('imagenes/uc_logo.png') }}"
                         alt="Universidad de Costa Rica"
                         class="h-full w-full object-contain"
                         onerror="this.onerror=null; this.style.display='none'">
                </div>
                <div class="ml-0">
                    <img src="{{ asset('imagenes/firma-horizontal-una-linea-reverso-rgb.png') }}"
                         alt="Firma UCR"
                         class="h-20 object-contain"
                         style="max-height:90px; display:block;"
                         onerror="this.onerror=null; this.style.display='none'">
                </div>
            </div>
        </div>

        {{-- Derecha: campanita + cerrar sesión + usuario --}}
        <div class="flex items-center gap-4">

            {{-- 🔔 Campanita de notificaciones --}}
            @if($usuarioActual)
                <div class="relative">
                    <button type="button"
                            id="notif-bell"
                            class="relative text-white text-xl focus:outline-none">
                        🔔
                        @if($unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 bg-red-600 text-xs px-1.5 rounded-full">
                                {{ $unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div id="notif-dropdown"
                         class="hidden absolute right-0 mt-2 w-80 bg-white text-gray-900 rounded-lg shadow-lg z-50">
                        <div class="px-3 py-2 border-b text-sm font-semibold">
                            Notificaciones
                        </div>

                        @forelse($unreadNotifications as $n)
                            @php
                                $msg = $n->data['message'] ?? 'Nueva notificación';
                                $url = $n->data['url'] ?? null;
                            @endphp

                            @if($url)
                                <a href="{{ $url }}"
                                   class="block px-3 py-2 text-sm hover:bg-gray-100">
                                    {{ $msg }}<br>
                                    <span class="text-xs text-gray-500">
                                        {{ $n->created_at->diffForHumans() }}
                                    </span>
                                </a>
                            @else
                                <a href="{{ route('notificaciones.show', $n->id) }}"
                                   class="block px-3 py-2 text-sm hover:bg-gray-100">
                                    {{ $msg }}<br>
                                    <span class="text-xs text-gray-500">
                                        {{ $n->created_at->diffForHumans() }}
                                    </span>
                                </a>
                            @endif
                        @empty
                            <div class="px-3 py-2 text-sm text-gray-500">
                                No hay notificaciones nuevas
                            </div>
                        @endforelse

                        <div class="px-3 py-2 border-t text-xs flex justify-between items-center">
                            <a href="{{ route('notificaciones.index') }}"
                               class="text-blue-600 hover:underline">
                                Ver todas
                            </a>

                            @if($unreadNotifications->count() > 0)
                                <form method="POST"
                                      action="{{ route('notificaciones.marcar-todas') }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-gray-500 hover:text-blue-600">
                                        Marcar todas como leídas
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Cerrar sesión --}}
            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 inline">
                @csrf
                <button type="submit"
                        class="text-white bg-transparent border-0 p-0 m-0 cursor-pointer text-sm font-medium">
                    Cerrar sesión
                </button>
            </form>

            {{-- Dropdown de usuario --}}
            <div class="relative">
                <button id="user-button"
                        type="button"
                        class="flex items-center gap-2 bg-white/10 text-white border border-white/20 rounded-full px-3 py-1.5 focus:outline-none">
                    <div class="rounded-full bg-white/20 overflow-hidden"
                         style="height:var(--user-avatar); width:var(--user-avatar);">
                        @if(!empty($avatarSrc))
                            <img id="user-avatar-top-img"
                                 src="{{ $avatarSrc }}"
                                 alt="avatar"
                                 class="w-full h-full object-cover">
                        @else
                            <span id="user-avatar-top-placeholder"
                                  class="block w-full h-full grid place-content-center">
                                👤
                            </span>
                        @endif
                    </div>
                    <span class="text-[12px] text-white">
                        {{ $nombreActual }}
                    </span>
                    <svg class="w-3 h-3 text-white"
                         viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 011.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>

                {{-- Dropdown perfil --}}
                <div id="user-dropdown"
                     class="hidden absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <div class="p-4">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $nombreActual }}
                        </p>
                        <p class="text-xs text-gray-500 mb-3">
                            {{ session('usuario_rol') ? strtoupper(session('usuario_rol')) : 'ROL DESCONOCIDO' }}
                        </p>

                        <form action="{{ route('perfil.update') }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Nombre</label>
                                <input type="text"
                                       name="nombre"
                                       value="{{ old('nombre', $usuarioActual->nombre ?? '') }}"
                                       class="w-full px-3 py-2 border rounded text-sm">
                            </div>

                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Apellido</label>
                                <input type="text"
                                       name="apellido"
                                       value="{{ old('apellido', $usuarioActual->apellido ?? '') }}"
                                       class="w-full px-3 py-2 border rounded text-sm">
                            </div>

                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Foto de perfil</label>
                                <input type="file"
                                       name="avatar"
                                       id="avatar-input"
                                       accept="image/*"
                                       class="text-xs">

                                <div id="avatar-preview"
                                     class="mt-2 w-20 h-20 rounded overflow-hidden border"
                                     style="display:{{ $avatarSrc ? 'block' : 'none' }};">
                                    @if($avatarSrc)
                                        <img src="{{ $avatarSrc }}"
                                             id="avatar-preview-img"
                                             class="w-full h-full object-cover">
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <button type="submit"
                                        class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded">
                                    Guardar
                                </button>
                                <a href="{{ route('declaraciones.index') }}"
                                   class="text-xs text-gray-500">
                                    Mi perfil
                                </a>
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
@php
    $hideSidebar = View::hasSection('hide_sidebar');
@endphp

<div class="w-full bg-white">
    <div class="flex flex-col md:flex-row w-full h-[calc(100vh-var(--topbar-h))]">
        @unless($hideSidebar)
            <aside class="w-full md:w-[var(--sidebar-w)] flex-shrink-0 bg-[var(--ucr-top-gray)] border-r border-gray-300 hidden md:block overflow-y-auto">
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

                    @if (Route::has('jornadas.index'))
                        <a href="{{ route('jornadas.index') }}" class="nav-item">
                            <img src="{{ asset('imagenes/jornadas.png') }}" alt="Jornadas" class="inline-block w-4 h-4 mr-2"
                                 onerror="this.style.display='none'">
                            <span class="font-medium">Jornadas</span>
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

        <main class="flex-grow overflow-auto bg-[var(--ucr-fondo)]">
            <div class="container mx-auto px-4 py-6">
                @includeIf('components.flash')

                @hasSection('content')
                    @yield('content')
                @elseif (View::hasSection('contenido'))
                    @yield('contenido')
                @endif
            </div>
        </main>
    </div>
</div>

{{-- FOOTER --}}
<footer class="bg-[var(--ucr-azul)] text-blue-100 text-center text-[11px] py-3">
    © {{ date('Y') }} Universidad de Costa Rica — Sistema de Declaraciones Juradas de Horario
</footer>

<script>
document.addEventListener('click', function(e) {
    // Dropdown usuario
    const userBtn = document.getElementById('user-button');
    const userDd  = document.getElementById('user-dropdown');

    if (userBtn && userDd) {
        if (userBtn.contains(e.target)) {
            userDd.classList.toggle('hidden');
        } else if (!userDd.contains(e.target)) {
            userDd.classList.add('hidden');
        }
    }

    // Dropdown notificaciones
    const bellBtn = document.getElementById('notif-bell');
    const bellDd  = document.getElementById('notif-dropdown');

    if (bellBtn && bellDd) {
        if (bellBtn.contains(e.target)) {
            e.stopPropagation();
            bellDd.classList.toggle('hidden');
        } else if (!bellDd.contains(e.target)) {
            bellDd.classList.add('hidden');
        }
    }
});

// Preview avatar + actualizar topbar
(function(){
    const avatarInput = document.getElementById('avatar-input');
    if (!avatarInput) return;

    avatarInput.addEventListener('change', function(){
        const file = this.files && this.files[0];
        const preview = document.getElementById('avatar-preview');
        let previewImg = document.getElementById('avatar-preview-img');
        const topImg = document.getElementById('user-avatar-top-img');
        const topPlaceholder = document.getElementById('user-avatar-top-placeholder');

        if (!file) {
            if (preview) preview.style.display = 'none';
            return;
        }

        const url = URL.createObjectURL(file);

        if (previewImg) {
            previewImg.src = url;
        } else if (preview) {
            previewImg = document.createElement('img');
            previewImg.id = 'avatar-preview-img';
            previewImg.className = 'w-full h-full object-cover';
            previewImg.src = url;
            preview.appendChild(previewImg);
        }

        if (preview) preview.style.display = 'block';

        if (topImg) {
            topImg.src = url;
        } else if (topPlaceholder) {
            const img = document.createElement('img');
            img.id = 'user-avatar-top-img';
            img.className = 'w-full h-full object-cover';
            img.src = url;
            topPlaceholder.replaceWith(img);
        }

        const cleanup = () => {
            try { URL.revokeObjectURL(url); } catch(e) {}
        };

        if (previewImg) {
            previewImg.onload = cleanup;
            previewImg.onerror = cleanup;
        }
    });
})();
</script>

</body>
</html>
