@extends('layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 relative">
    <div class="bg-white/90 backdrop-blur border border-gray-200 rounded-2xl shadow-[0_10px_30px_rgba(2,6,23,0.06)] overflow-hidden">
        <!-- Header -->
        <div class="relative px-8 py-7 flex items-center justify-between bg-gradient-to-r from-blue-600 via-blue-600 to-indigo-600">
            <div>
                <h2 class="text-2xl font-semibold text-white tracking-tight">Lista de Horarios</h2>
                <p class="text-blue-100 text-sm mt-1">Gestión de horarios registrados</p>
            </div>
            <a href="{{ route('horarios.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-white rounded-lg hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 transition">
                <!-- plus icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                </svg>
                Nuevo Horario
            </a>

            <!-- Soft glow -->
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
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold">Día</th>
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold">Hora inicio</th>
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold">Hora fin</th>
                            <th class="bg-gray-50/90 sticky top-0 z-10 text-left py-3.5 px-4 font-semibold rounded-tr-xl">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($horarios as $h)
                            <tr class="group transition-colors odd:bg-white even:bg-slate-50 hover:bg-blue-50/50">
                                <td class="py-3.5 px-4 text-gray-900">{{ $h->id_horario }}</td>

                                <td class="py-3.5 px-4">
                                    @php $isUcr = $h->tipo === 'ucr'; @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $isUcr ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $isUcr ? 'bg-blue-500' : 'bg-amber-500' }}"></span>
                                        {{ $isUcr ? 'UCR' : 'Otra institución' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-gray-900 font-medium">{{ $h->lugar ?? '-' }}</td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center rounded-md bg-slate-100 text-slate-700 px-2 py-0.5 text-xs font-semibold">
                                        {{ $h->dia }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-slate-700 font-mono">{{ $h->hora_inicio }}</td>
                                <td class="py-3.5 px-4 text-slate-700 font-mono">{{ $h->hora_fin }}</td>

                                <td class="py-3.5 px-4">
                                    <form action="{{ route('horarios.destroy', $h->id_horario) }}" method="POST" class="inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Eliminar horario"
                                            class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700
                                                   hover:bg-red-100 hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0l1-2h4l1 2M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-gray-500">
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

    <!-- Cajita de confirmación (popup ligero) -->
    <div id="popupConfirm"
         class="hidden absolute left-1/2 top-32 -translate-x-1/2 z-50
                w-[22rem] rounded-xl border border-gray-200 bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)]
                p-5 text-center opacity-0 scale-95 transition duration-150">
        <p class="text-gray-900 font-medium">¿Eliminar este horario?</p>
        <p class="text-xs text-gray-500 mt-1">Esta acción no se puede deshacer.</p>
        <div class="mt-4 flex justify-center gap-2">
            <button id="cancelPopup"
                    class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button id="confirmPopup"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let formToDelete = null;
    const popup = document.getElementById('popupConfirm');

    function showPopup() {
        popup.classList.remove('hidden');
        // animación: fade + scale
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
});
</script>
@endsection
