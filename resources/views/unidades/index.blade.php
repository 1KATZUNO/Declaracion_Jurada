@extends('layout')
@section('titulo', 'UA – Unidades Académicas')

@section('contenido')
<div class="container mx-auto w-full max-w-7xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">UA – UNIDADES ACADÉMICAS</h2>
            <p class="text-gray-500 text-sm mt-1">
                Gestione y consulte las unidades académicas asociadas a cada sede universitaria.
            </p>
        </div>

        {{-- CONTENIDO --}}
        <div class="p-8 space-y-8">

            {{-- CRITERIO DE BÚSQUEDA --}}
            <section>
                <h3 class="text-xs font-semibold text-gray-600 tracking-wider uppercase">Criterio de búsqueda</h3>
                <div class="mt-4">
                    <form method="GET"
                          action="{{ route('unidades.index') }}"
                          class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div class="col-span-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre de la unidad
                            </label>
                            <input id="search" type="text" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Ej: Escuela de Ciencias Económicas"
                                   class="w-full rounded-md border-gray-300 focus:border-blue-600 focus:ring-blue-600 text-sm">
                        </div>

                        <div class="col-span-1">
                            <label for="sede_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Sede
                            </label>
                            <select id="sede_id" name="sede_id"
                                    class="w-full rounded-md border-gray-300 focus:border-blue-600 focus:ring-blue-600 text-sm">
                                <option value="">Todas</option>
                                @foreach($sedes as $s)
                                    <option value="{{ $s->id_sede }}" @selected(request('sede_id') == $s->id_sede)>
                                        {{ $s->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1 flex items-end gap-3">
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-md bg-blue-700 text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600">
                                Buscar
                            </button>
                            <a href="{{ route('unidades.index') }}"
                               class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </section>

            {{-- ACCESOS FRECUENTES --}}
            <section>
                <h3 class="text-xs font-semibold text-gray-600 tracking-wider uppercase">Accesos frecuentes</h3>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <a href="{{ route('unidades.create') }}"
                       class="flex items-center gap-3 rounded-md border border-gray-200 px-4 py-3 hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-800">Agregar Unidad Académica</span>
                    </a>

                    <button type="button" id="btnBuscarEditarUA"
                            class="flex items-center gap-3 rounded-md border border-gray-200 px-4 py-3 hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536M4 20h4.586a1 1 0 00.707-.293l9.439-9.439a1 1 0 000-1.414L16.12 4.293a1 1 0 00-1.414 0L5.268 13.732A1 1 0 005 14.439V19a1 1 0 001 1z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-800">Buscar y editar</span>
                    </button>
                </div>
            </section>

            {{-- LISTA DE UNIDADES --}}
            <section>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-xs font-semibold text-gray-600 tracking-wider uppercase">UA – Lista de unidades</h3>
                </div>

                <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nombre de la Unidad</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Sede Asociada</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    @forelse ($unidades as $u)
        <tr class="hover:bg-gray-50 transition-colors">
            {{-- Nombre --}}
            <td class="py-4 px-4 text-sm text-gray-900 font-medium">
                {{ $u->nombre }}
            </td>

            {{-- Sede asociada --}}
            <td class="py-4 px-4 text-sm text-gray-600">
                {{ $u->sede->nombre ?? '—' }}
            </td>

            {{-- Acciones --}}
            <td class="py-4 px-4 text-sm">
                <div class="flex items-center gap-2">

                    {{-- Editar --}}
                    <a href="{{ route('unidades.edit', $u->id_unidad) }}"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-yellow-300 bg-yellow-50 text-yellow-800 hover:bg-yellow-100">
                        Editar
                    </a>

                    @if($u->declaraciones_count == 0)
                        {{-- Eliminar habilitado --}}
                        <form action="{{ route('unidades.destroy', $u->id_unidad) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar esta unidad académica?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-red-300 bg-red-50 text-red-700 hover:bg-red-100">
                                Eliminar
                            </button>
                        </form>
                    @else
                        {{-- No eliminable --}}
                        <button type="button"
                                onclick="showToast('No se puede eliminar esta unidad académica porque tiene declaraciones juradas asociadas.', 'warning')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                                title="No se puede eliminar: tiene declaraciones juradas asociadas. Se marcará INACTIVA desde el sistema.">
                            DJ Asociada
                        </button>
                    @endif

                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="py-12 text-center text-sm text-gray-500">
                No se encontraron unidades con los criterios actuales.
            </td>
        </tr>
    @endforelse
</tbody>

                    </table>
                </div>

                @if(method_exists($unidades, 'links'))
                    <div class="mt-4">
                        {{ $unidades->appends(request()->query())->links() }}
                    </div>
                @endif
            </section>

        </div>
    </div>
</div>

{{-- MODAL Buscar y editar UA --}}
<div id="modalBuscarUA" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/30"></div>

    <div class="relative mx-auto mt-24 w-11/12 max-w-xl bg-white rounded-lg shadow-lg border">
        <div class="px-5 py-4 border-b">
            <h3 class="text-base font-semibold text-gray-900">Buscar unidad académica</h3>
            <p class="text-sm text-gray-500 mt-1">Escriba parte del nombre y seleccione la unidad a editar.</p>
        </div>

        <div class="p-5 space-y-3">
            <input id="uaQuery" type="text" placeholder="Ej.: Escuela de Matemática"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:border-blue-600"/>
            <div id="uaResultados" class="max-h-72 overflow-y-auto border rounded-md divide-y">
                {{-- resultados dinámicos --}}
            </div>
        </div>

        <div class="px-5 py-3 border-t flex justify-end gap-2">
            <button type="button" id="cerrarModalUA"
                    class="px-4 py-2 text-sm bg-white border rounded-md hover:bg-gray-50">
                Cerrar
            </button>
        </div>
    </div>
</div>

{{-- Script del modal --}}
<script>
(function () {
    const btn = document.getElementById('btnBuscarEditarUA');
    const modal = document.getElementById('modalBuscarUA');
    const closeBtn = document.getElementById('cerrarModalUA');
    const input = document.getElementById('uaQuery');
    const list = document.getElementById('uaResultados');

    if (!btn || !modal || !closeBtn || !input || !list) return;

    const baseEditUrl = @json(url('unidades'));                // /unidades/{id}/edit
    const catalogoUrl = @json(route('unidades.catalogo'));     // ruta al catálogo JSON
    let lastController = null;

    const abrir = () => {
        modal.classList.remove('hidden');
        list.innerHTML = '';
        input.value = '';
        input.focus();
        buscarUnidades(''); // carga inicial
    };

    const cerrar = () => {
        modal.classList.add('hidden');
    };

    btn.addEventListener('click', abrir);
    closeBtn.addEventListener('click', cerrar);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) cerrar();
    });

    async function buscarUnidades(q) {
        if (lastController) lastController.abort();
        const controller = new AbortController();
        lastController = controller;

        list.innerHTML = '<div class="p-3 text-sm text-gray-500">Buscando…</div>';

        try {
            const resp = await fetch(catalogoUrl + '?solo_activas=true', {
                signal: controller.signal
            });

            if (!resp.ok) throw new Error('HTTP ' + resp.status);

            const data = await resp.json(); // [{ id, nombre, id_sede }, ...]

            const filtradas = q
                ? data.filter(u => (u.nombre || '').toLowerCase().includes(q.toLowerCase()))
                : data;

            if (!filtradas.length) {
                list.innerHTML = '<div class="p-3 text-sm text-gray-500">Sin resultados</div>';
                return;
            }

            list.innerHTML = filtradas.map(u => {
                const id = u.id; // viene de id_unidad as id
                if (!id) return '';
                return `
                    <div class="p-3 hover:bg-gray-50 flex items-center justify-between">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">${u.nombre}</div>
                            <div class="text-gray-500">ID: ${id}</div>
                        </div>
                        <a href="${baseEditUrl}/${id}/edit"
                           class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100">
                            Editar
                        </a>
                    </div>
                `;
            }).join('') || '<div class="p-3 text-sm text-gray-500">Sin resultados</div>';

        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error(err);
                list.innerHTML = '<div class="p-3 text-sm text-red-600">Error al buscar unidades.</div>';
            }
        }
    }

    input.addEventListener('input', (e) => {
        buscarUnidades(e.target.value.trim());
    });
})();
</script>
@endsection
