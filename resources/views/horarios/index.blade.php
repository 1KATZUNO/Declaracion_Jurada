@extends('layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 relative">
    <div class="bg-white/90 backdrop-blur border border-gray-200 rounded-2xl shadow-[0_10px_30px_rgba(2,6,23,0.06)] overflow-hidden">
        <!-- Header -->
        <div class="relative px-8 py-7 flex items-center justify-between bg-gradient-to-r from-blue-600 via-blue-600 to-indigo-600">
            <div>
                <h2 class="text-2xl font-semibold text-white tracking-tight">Lista de Horarios</h2>
                <p class="text-blue-50 text-sm mt-1">Gestión de horarios registrados</p>
            </div>
            <a href="{{ route('horarios.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-white rounded-lg hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                </svg>
                Nuevo Horario
            </a>
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -inset-8 bg-white/10 blur-2xl rounded-full"></div>
            </div>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-0">
                    <thead>
                        <tr class="text-xs uppercase text-gray-600">
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold rounded-tl-xl">#</th>
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold">Tipo</th>
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold">Lugar</th>

                            <!-- Reemplazamos las columnas individuales por una sola columna "Detalles" -->
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold">Detalles</th>

                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold rounded-tr-xl">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($horarios as $h)
                            @php $isUcr = $h->tipo === 'ucr'; @endphp
                            <tr class="group transition-colors odd:bg-white even:bg-slate-50 hover:bg-blue-50/50"
                                data-id="{{ $h->id_horario }}"
                                data-tipo="{{ $h->tipo }}"
                                data-lugar="{{ $h->lugar }}"
                                data-dia="{{ $h->dia }}"
                                data-hora_inicio="{{ \Illuminate\Support\Str::of($h->hora_inicio)->substr(0,5) }}"
                                data-hora_fin="{{ \Illuminate\Support\Str::of($h->hora_fin)->substr(0,5) }}"
                            >
                                <td class="py-3.5 px-4 text-gray-900">{{ $h->id_horario }}</td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $isUcr ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $isUcr ? 'bg-blue-500' : 'bg-amber-500' }}"></span>
                                        {{ $isUcr ? 'UCR' : 'Otra institución' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-gray-900 font-medium">{{ $h->lugar ?? '-' }}</td>

                                <!-- Nueva celda: Detalles (muestra cada detalle en su propia línea) -->
                                <td class="py-3.5 px-4">
                                    @if($h->detalles && $h->detalles->isNotEmpty())
                                        @foreach($h->detalles as $d)
                                            <div class="mb-1">
                                                <span class="text-sm font-medium text-gray-900">{{ $d->dia }}</span>
                                                <span class="ml-2 text-xs font-mono text-slate-700">{{ \Illuminate\Support\Str::substr($d->hora_inicio,0,5) }}‑{{ \Illuminate\Support\Str::substr($d->hora_fin,0,5) }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        {{-- Compatibilidad: si no hay detalles, mostrar campos del padre --}}
                                        <div class="mb-1">
                                            <span class="text-sm font-medium text-gray-900">{{ $h->dia ?? '-' }}</span>
                                            <span class="ml-2 text-xs font-mono text-slate-700">{{ $h->hora_inicio ? \Illuminate\Support\Str::substr($h->hora_inicio,0,5) : '‑' }}‑{{ $h->hora_fin ? \Illuminate\Support\Str::substr($h->hora_fin,0,5) : '‑' }}</span>
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 flex items-center gap-2">
                                    <!-- EDITAR -->
                                    <button type="button"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 transition open-edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536M16.5 3.964a2.25 2.25 0 113.182 3.182L7.5 19.5 3 21l1.5-4.5 12-12z"/>
                                        </svg>
                                        Editar
                                    </button>

                                    <!-- ELIMINAR (igual) -->
                                    <form action="{{ route('horarios.destroy', $h->id_horario) }}" method="POST" class="inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Eliminar horario"
                                            class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-gray-500">
                                    No hay horarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $horarios->links() }}
            </div>
        </div>
    </div>

    <!-- POPUP ELIMINAR (igual) -->
    <div id="popupConfirm"
         class="hidden absolute left-1/2 top-32 -translate-x-1/2 z-50 w-[22rem] rounded-xl border border-gray-200 bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)] p-5 text-center opacity-0 scale-95 transition duration-150">
        <p class="text-gray-900 font-medium">¿Eliminar este horario?</p>
        <p class="text-xs text-gray-500 mt-1">Esta acción no se puede deshacer.</p>
        <div class="mt-4 flex justify-center gap-2">
            <button id="cancelPopup" class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">Cancelar</button>
            <button id="confirmPopup" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">Eliminar</button>
        </div>
    </div>

    <!-- POPUP EDITAR (solo: tipo, lugar*, día, horas) -->
    <div id="popupEdit"
         class="hidden absolute left-1/2 top-40 -translate-x-1/2 z-50 w-[28rem] rounded-xl border border-gray-200 bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)] p-5 opacity-0 scale-95 transition duration-150">
        <h3 class="text-base font-semibold text-gray-900">Editar horario</h3>

        <form id="formEdit" method="POST" class="mt-4 space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="text-xs text-gray-600">Tipo</label>
                <select name="tipo" id="edit_tipo" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                    <option value="ucr">UCR</option>
                    <option value="externo">Otra institución</option>
                </select>
            </div>

            <div id="wrap_lugar">
                <label class="text-xs text-gray-600">Lugar (solo otra institución)</label>
                <input type="text" name="lugar" id="edit_lugar" placeholder="-"
                       class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="text-xs text-gray-600">Día</label>
                <select name="dia" id="edit_dia" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                    <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                    <option>Jueves</option><option>Viernes</option><option>Sábado</option><option>Domingo</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600">Hora inicio</label>
                    <input type="time" name="hora_inicio" id="edit_hora_inicio"
                           class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-xs text-gray-600">Hora fin</label>
                    <input type="time" name="hora_fin" id="edit_hora_fin"
                           class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" id="cancelEdit"
                        class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus-visible:ring-2 focus-visible:ring-blue-400 transition">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    /* -------- ELIMINAR (igual) -------- */
    let formToDelete = null;
    const popup = document.getElementById('popupConfirm');

    function showPopup() {
        popup.classList.remove('hidden');
        requestAnimationFrame(() => {
            popup.classList.remove('opacity-0','scale-95');
            popup.classList.add('opacity-100','scale-100');
        });
    }
    function hidePopup() {
        popup.classList.add('opacity-0','scale-95');
        popup.classList.remove('opacity-100','scale-100');
        setTimeout(() => popup.classList.add('hidden'), 150);
    }

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            formToDelete = form;
            showPopup();
        });
    });

    document.getElementById('cancelPopup').addEventListener('click', () => {
        formToDelete = null;
        hidePopup();
    });
    document.getElementById('confirmPopup').addEventListener('click', () => {
        if (formToDelete) formToDelete.submit();
        hidePopup();
    });

    /* -------- EDITAR (solo campos solicitados) -------- */
    const editBox = document.getElementById('popupEdit');
    const formEdit = document.getElementById('formEdit');
    const selTipo = document.getElementById('edit_tipo');
    const wrapLugar = document.getElementById('wrap_lugar');
    const inpLugar = document.getElementById('edit_lugar');
    const selDia = document.getElementById('edit_dia');
    const inpHi = document.getElementById('edit_hora_inicio');
    const inpHf = document.getElementById('edit_hora_fin');

    function showEdit() {
        editBox.classList.remove('hidden');
        requestAnimationFrame(() => {
            editBox.classList.remove('opacity-0','scale-95');
            editBox.classList.add('opacity-100','scale-100');
        });
    }
    function hideEdit() {
        editBox.classList.add('opacity-0','scale-95');
        editBox.classList.remove('opacity-100','scale-100');
        setTimeout(() => editBox.classList.add('hidden'), 150);
    }

    function toggleLugarByTipo() {
        const isUcr = selTipo.value === 'ucr';
        if (isUcr) {
            wrapLugar.classList.add('hidden');
            inpLugar.required = false;    // <-- si querés forzar obligatorio en externo, cambia abajo
            inpLugar.value = '';
        } else {
            wrapLugar.classList.remove('hidden');
            // Si querés que sea OBLIGATORIO cuando es "externo", descomenta:
            // inpLugar.required = true;
        }
    }
    selTipo.addEventListener('change', toggleLugarByTipo);

    // Abrir modal con datos de la fila
    document.querySelectorAll('.open-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const tr = btn.closest('tr');
            const id   = tr.dataset.id;
            const tipo = tr.dataset.tipo || 'ucr';
            const lugar = tr.dataset.lugar || '';
            const dia = tr.dataset.dia || 'Lunes';
            const hi = tr.dataset.hora_inicio || '';
            const hf = tr.dataset.hora_fin || '';

            // action -> PUT /horarios/{id}
            formEdit.action = "{{ url('horarios') }}/" + id;

            // normaliza “otra” a “externo” por si tuvieras ese valor en DB
            selTipo.value = (tipo === 'externo' || tipo === 'otra') ? 'externo' : 'ucr';
            inpLugar.value = lugar;
            selDia.value = dia;
            inpHi.value = hi;
            inpHf.value = hf;

            toggleLugarByTipo();
            showEdit();
        });
    });

    document.getElementById('cancelEdit').addEventListener('click', hideEdit);
});
</script>
@endsection
