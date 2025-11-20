@extends('layout')
@csrf
@section('titulo', 'Jornadas')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 relative">
  <div class="bg-white/90 backdrop-blur border border-gray-200 rounded-2xl shadow-[0_10px_30px_rgba(2,6,23,0.06)] overflow-hidden">
    <!-- HEADER -->
    <div class="relative px-8 py-7 flex items-center justify-between bg-blue-600">
      <div>
        <h2 class="text-2xl font-semibold text-white tracking-tight">Jornadas laborales</h2>
        <p class="text-blue-100 text-sm mt-1">Gestión de jornadas registradas en el sistema</p>
      </div>

      {{-- Mostrar "Modificar jornada TC" que abre el edit de la jornada TC --}}
      @if(!empty($tcJornada))
        <a href="{{ route('jornadas.edit', $tcJornada->id_jornada) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-white rounded-lg hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h10M11 12h10M11 19h10M6 5h.01M6 12h.01M6 19h.01"/>
          </svg>
          Modificar jornada (TC)
        </a>
      @endif
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
                  @if($j->tipo === 'TC')
                    <a href="{{ route('jornadas.edit', $j->id_jornada) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 transition">
                      Editar
                    </a>
                    {{-- opción de eliminar sigue deshabilitada en UI según flujo --}}
                    <span class="text-xs text-gray-400 italic">Protegida</span>
                  @else
                    {{-- Fracciones automáticas: no permitir edición desde la lista --}}
                    <span class="inline-flex items-center gap-2 rounded-md px-3 py-1 text-xs font-medium text-gray-500 bg-gray-50 border border-gray-200">
                      Protegida
                    </span>
                  @endif
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
