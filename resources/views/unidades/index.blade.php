@extends('layout')
@section('titulo', 'UA – Unidades Académicas')
@section('contenido')
  {{-- Título principal --}}
  <header class="mb-6">
    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-[var(--ucr-azul)]">
      UA – UNIDADES ACADÉMICAS
    </h1>
    <p class="text-sm text-gray-600 mt-1">
      Gestione y consulte las unidades académicas asociadas a cada sede universitaria.
    </p>
  </header>

  {{-- CRITERIO DE BÚSQUEDA --}}
  <section class="mb-6">
    <h2 class="text-sm font-extrabold text-[var(--ucr-azul)] uppercase tracking-wider">Criterio de búsqueda</h2>

    <form method="GET" action="{{ route('unidades.index') }}" class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
      {{-- Nombre --}}
      <div>
        <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">Nombre de la unidad</label>
        <input
          id="search" name="search" type="text" value="{{ request('search') }}"
          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--ucr-azul)] focus:border-[var(--ucr-azul)]"
          placeholder="Ej: Escuela de Ciencias Económicas">
      </div>

      {{-- Sede --}}
      <div>
        <label for="sede_id" class="block text-xs font-semibold text-gray-700 mb-1">Sede</label>
        <select
          id="sede_id" name="sede_id"
          class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--ucr-azul)] focus:border-[var(--ucr-azul)]">
          <option value="">Todas</option>
          @foreach($sedes as $s)
            <option value="{{ $s->id_sede }}" @selected(request('sede_id') == $s->id_sede)>{{ $s->nombre }}</option>
          @endforeach
        </select>
      </div>

      {{-- Botones --}}
      <div class="flex items-end gap-2">
        <button type="submit"
          class="rounded-md text-white text-sm font-semibold px-4 py-2"
          style="background: var(--ucr-azul);">
          Buscar
        </button>
        <a href="{{ route('unidades.index') }}"
           class="rounded-md text-white text-sm font-semibold px-4 py-2 hover:opacity-90"
           style="background:#6B7280;">
          Limpiar
        </a>
      </div>
    </form>
  </section>

  {{-- ACCESOS FRECUENTES (sin “Eliminar”) --}}
  <section class="mb-6">
    <h2 class="text-sm font-extrabold text-[var(--ucr-azul)] uppercase tracking-wider">Accesos frecuentes</h2>
    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">

      {{-- Agregar --}}
      <a href="{{ route('unidades.create') }}"
         class="flex items-center gap-3 rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
        <span class="grid h-6 w-6 place-content-center rounded bg-gray-100 text-gray-700 text-lg" aria-hidden="true">＋</span>
        <span class="text-sm font-medium text-gray-800">Agregar Unidad Académica</span>
      </a>

      {{-- Buscar y editar  --}}
      <button type="button" id="btnBuscarEditarUA"
              class="flex items-center gap-3 rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
        <span class="grid h-6 w-6 place-content-center rounded bg-gray-100 text-gray-700 text-lg" aria-hidden="true">✎</span>
        <span class="text-sm font-medium text-gray-800">Buscar y editar</span>
      </button>
    </div>
  </section>

  {{-- LISTA DE UNIDADES (tabla con scroll y header sticky) --}}
  <section>
    <h2 class="text-sm font-extrabold text-[var(--ucr-azul)] uppercase tracking-wider mb-3">UA – Lista de unidades</h2>

    <div class="bg-white border border-gray-200 rounded-md">
      <div class="max-h-[420px] overflow-y-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 sticky top-0 border-b border-gray-200 z-10">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Nombre de la Unidad</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Sede Asociada</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse ($unidades as $u)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">{{ $u->nombre }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $u->sede->nombre ?? '—' }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <a href="{{ route('unidades.edit', $u->id_unidad) }}"
                       class="inline-flex items-center gap-1 rounded border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-800 hover:bg-gray-50">
                      ✏️ Editar
                    </a>
                    <form action="{{ route('unidades.destroy', $u->id_unidad) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar esta unidad académica?')">
                      @csrf @method('DELETE')
                      <button
                        class="inline-flex items-center gap-1 rounded border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-800 hover:bg-gray-50">
                        🗑️ Eliminar
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                  No se encontraron unidades con los criterios actuales.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  {{--  Buscar y editar --}}
  <div id="modalBuscarUA" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30"></div>

    <div class="relative mx-auto mt-24 w-11/12 max-w-xl bg-white rounded-lg shadow-lg border">
      <div class="px-5 py-4 border-b">
        <h3 class="text-base font-semibold text-gray-900">Buscar unidad académica</h3>
        <p class="text-sm text-gray-500 mt-1">Escriba parte del nombre y seleccione la unidad a editar.</p>
      </div>

      <div class="p-5 space-y-3">
        <input id="uaQuery" type="text" placeholder="Ej.: Escuela de Matemática"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-[var(--ucr-azul)] focus:border-[var(--ucr-azul)]"/>
        <div id="uaResultados" class="max-h-72 overflow-y-auto border rounded-md divide-y">
          {{-- resultados dinámicos --}}
        </div>
      </div>

      <div class="px-5 py-3 border-t flex justify-end gap-2">
        <button type="button" id="cerrarModalUA"
                class="px-4 py-2 text-sm bg-white border rounded-md hover:bg-gray-50">Cerrar</button>
      </div>
    </div>
  </div>

  {{-- JS del modal --}}
  <script>
  (() => {
    const btn = document.getElementById('btnBuscarEditarUA');
    const modal = document.getElementById('modalBuscarUA');
    const closeBtn = document.getElementById('cerrarModalUA');
    const input = document.getElementById('uaQuery');
    const list = document.getElementById('uaResultados');

    const abrir = () => { modal.classList.remove('hidden'); list.innerHTML=''; input.value=''; input.focus(); }
    const cerrar = () => modal.classList.add('hidden');

    btn.addEventListener('click', abrir);
    closeBtn.addEventListener('click', cerrar);
    modal.addEventListener('click', (e) => { if (e.target === modal) cerrar(); });

    let lastController = null;

    async function buscarUnidades(q) {
     
      if (lastController) lastController.abort();
      const controller = new AbortController();
      lastController = controller;

      list.innerHTML = '<div class="p-3 text-sm text-gray-500">Buscando…</div>';
      try {
        const resp = await fetch(`/catalogos/unidades?solo_activas=true`, { signal: controller.signal });
        const data = await resp.json();

        const filtradas = q
          ? data.filter(u => (u.nombre || '').toLowerCase().includes(q.toLowerCase()))
          : data;

        if (!filtradas.length) {
          list.innerHTML = '<div class="p-3 text-sm text-gray-500">Sin resultados</div>';
          return;
        }

        list.innerHTML = filtradas.map(u => `
          <div class="p-3 hover:bg-gray-50 flex items-center justify-between">
            <div class="text-sm">
              <div class="font-medium text-gray-900">${u.nombre}</div>
              <div class="text-gray-500">ID: ${u.id}</div>
            </div>
            <a href="/unidades/${u.id}/edit"
               class="px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded hover:bg-indigo-100">
              Editar
            </a>
          </div>
        `).join('');

      } catch (err) {
        if (err.name !== 'AbortError') {
          list.innerHTML = '<div class="p-3 text-sm text-red-600">Error al buscar.</div>';
        }
      }
    }

    input.addEventListener('input', (e) => buscarUnidades(e.target.value.trim()));
    // carga inicial sin filtro
    buscarUnidades('');
  })();
  </script>
@endsection
