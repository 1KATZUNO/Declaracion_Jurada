@extends('layout')
@csrf
@section('titulo', 'Jornadas')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 relative">
  <div class="bg-white/90 backdrop-blur border border-gray-200 rounded-2xl shadow-[0_10px_30px_rgba(2,6,23,0.06)] overflow-hidden">
    <!-- HEADER -->
    <div class="relative px-8 py-7 flex items-center justify-between bg-gradient-to-r from-blue-600 via-blue-600 to-indigo-600">
      <div>
        <h2 class="text-2xl font-semibold text-white tracking-tight">Jornadas laborales</h2>
        <p class="text-blue-100 text-sm mt-1">Gestión de jornadas registradas en el sistema</p>
      </div>
      <a href="{{ route('jornadas.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-white rounded-lg hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
        </svg>
        Nueva Jornada
      </a>
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
              <th class="bg-gray-50/90 text-left py-3.5 px-4 font-semibold rounded-tl-xl">#</th>
              <th class="bg-gray-50/90 text-left py-3.5 px-4 font-semibold">Tipo</th>
              <th class="bg-gray-50/90 text-left py-3.5 px-4 font-semibold">Horas por semana</th>
              <th class="bg-gray-50/90 text-left py-3.5 px-4 font-semibold rounded-tr-xl">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            @forelse($jornadas as $j)
              <tr class="group transition-colors odd:bg-white even:bg-slate-50 hover:bg-blue-50/50">
                <td class="py-3.5 px-4 text-gray-900">{{ $j->id_jornada }}</td>
                <td class="py-3.5 px-4 text-gray-900 font-medium">{{ $j->tipo }}</td>
                <td class="py-3.5 px-4 text-slate-700 font-mono">{{ $j->horas_por_semana }}</td>
                <td class="py-3.5 px-4 flex items-center gap-2">
                  <a href="{{ route('jornadas.edit', $j->id_jornada) }}"
                     class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536M16.5 3.964a2.25 2.25 0 113.182 3.182L7.5 19.5 3 21l1.5-4.5 12-12z"/>
                    </svg>
                    Editar
                  </a>

                  {{-- BOTÓN ELIMINAR CON POPUP --}}
                  <form action="{{ route('jornadas.destroy', $j->id_jornada) }}" method="POST" class="inline delete-form">
                    @csrf @method('DELETE')
                    <button type="submit" title="Eliminar jornada"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 hover:border-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">
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
                <td colspan="4" class="text-center py-10 text-gray-500">No hay jornadas registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-6">
        {{ $jornadas->links() }}
      </div>
    </div>
  </div>

  {{-- POPUP ELIMINAR --}}
  <div id="popupConfirm"
       class="hidden absolute left-1/2 top-32 -translate-x-1/2 z-50
              w-[22rem] rounded-xl border border-gray-200 bg-white shadow-[0_10px_30px_rgba(2,6,23,0.08)]
              p-5 text-center opacity-0 scale-95 transition duration-150">
    <p class="text-gray-900 font-medium">¿Eliminar esta jornada?</p>
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

{{-- Script para control del popup --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
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
});
</script>
@endsection
