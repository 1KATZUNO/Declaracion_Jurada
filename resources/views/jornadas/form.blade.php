@extends('layout')
@csrf
@section('titulo', ($mode === 'create' ? 'Nueva Jornada' : 'Editar Jornada'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
  <div class="bg-white/90 backdrop-blur border border-gray-200 rounded-2xl shadow-[0_10px_30px_rgba(2,6,23,0.06)] overflow-hidden">
    <div class="relative px-8 py-7 bg-gradient-to-r from-blue-600 via-blue-600 to-indigo-600">
      <h2 class="text-2xl font-semibold text-white tracking-tight">
        {{ $mode === 'create' ? 'Nueva Jornada' : 'Editar Jornada' }}
      </h2>
      <p class="text-blue-50 text-sm mt-1">Ingrese cuántas horas por semana corresponde a esta jornada. Si modifica la jornada <strong>Tiempo completo (TC)</strong>, el sistema recalculará automáticamente las jornadas fraccionarias existentes (1/8,1/4,1/2,3/4).</p>
    </div>

    <div class="p-8">
      @if($errors->any())
        <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-800">
          <ul class="list-disc pl-6 text-sm">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST"
            action="{{ $mode === 'create' ? route('jornadas.store') : route('jornadas.update', $jornada->id_jornada) }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- Tipo de jornada --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
            @if($mode === 'edit')
              {{-- Mostrar tipo como texto y enviar value oculto; solo se edita horas_por_semana --}}
              <div class="px-3 py-2 rounded border bg-gray-50 text-sm text-gray-700">{{ $jornada->tipo }}</div>
              <input type="hidden" name="tipo" value="{{ $jornada->tipo }}">
              <p class="text-xs text-gray-500 mt-1">Al guardar, las horas ingresadas se tomarán como base y se propagarán a las demás conversiones existentes.</p>
            @else
              <select id="tipoSelect" name="tipo"
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white"
                      required>
                <option value="" disabled {{ old('tipo', $jornada->tipo) ? '' : 'selected' }}>Seleccione...</option>
                <option value="1/8" {{ old('tipo', $jornada->tipo) === '1/8' ? 'selected' : '' }}>1/8</option>
                <option value="1/4" {{ old('tipo', $jornada->tipo) === '1/4' ? 'selected' : '' }}>1/4</option>
                <option value="1/2" {{ old('tipo', $jornada->tipo) === '1/2' ? 'selected' : '' }}>1/2</option>
                <option value="3/4" {{ old('tipo', $jornada->tipo) === '3/4' ? 'selected' : '' }}>3/4</option>
                <option value="TC" {{ old('tipo', $jornada->tipo) === 'TC' ? 'selected' : '' }}>Tiempo completo (TC)</option>
              </select>
            @endif
          </div>

          {{-- Horas por semana (editable) --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Horas por semana</label>
            <input type="number"
                   id="horasInput"
                   name="horas_por_semana"
                   value="{{ old('horas_por_semana', $jornada->horas_por_semana) }}"
                   min="1" max="168"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white text-gray-700">
            <p id="notaPropagacion" class="text-xs text-gray-500 mt-1 hidden">Al guardar, si este registro es <strong>TC</strong>, las jornadas fraccionarias existentes se recalcularán automáticamente.</p>
          </div>
        </div>

        {{-- Panel de previsualización: muestra conversiones según TC --}}
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded p-4 text-sm">
          <div class="font-medium mb-2">Previsualización (calculada desde TC = <span id="tcValueDisplay">{{ $tcHoras ?? 40 }}</span> h)</div>
          <div class="grid grid-cols-2 md:grid-cols-8 gap-3">
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">1/8</div>
              <div id="pv_1_8" class="font-semibold">— h</div>
            </div>
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">1/4</div>
              <div id="pv_1_4" class="font-semibold">— h</div>
            </div>
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">3/8</div>
              <div id="pv_3_8" class="font-semibold">— h</div>
            </div>
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">1/2</div>
              <div id="pv_1_2" class="font-semibold">— h</div>
            </div>
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">5/8</div>
              <div id="pv_5_8" class="font-semibold">— h</div>
            </div>
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">3/4</div>
              <div id="pv_3_4" class="font-semibold">— h</div>
            </div>
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">7/8</div>
              <div id="pv_7_8" class="font-semibold">— h</div>
            </div>
            <div class="p-2 bg-white border rounded text-center">
              <div class="text-xs text-gray-500">TC (1)</div>
              <div id="pv_tc" class="font-semibold">— h</div>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-8">
          <a href="{{ route('jornadas.index') }}"
             class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Cancelar
          </a>
          <button type="submit"
                  class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
            {{ $mode === 'create' ? 'Crear jornada' : 'Guardar cambios' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const tipoSelect = document.getElementById('tipoSelect');
  const horasInput = document.getElementById('horasInput');
  const tcDisplay = document.getElementById('tcValueDisplay');
  const notaPropagacion = document.getElementById('notaPropagacion');

  // tcHoras provisto por el servidor (valor actual de TC)
  const tcHorasServer = Number(@json($tcHoras ?? 40));

  function calcularDesdeTC(tc) {
    // mapa completo de fracciones
    return {
      '1/8': Math.round((tc * 0.125) * 100) / 100,
      '1/4': Math.round((tc * 0.25)  * 100) / 100,
      '3/8': Math.round((tc * 0.375) * 100) / 100,
      '1/2': Math.round((tc * 0.5)   * 100) / 100,
      '5/8': Math.round((tc * 0.625) * 100) / 100,
      '3/4': Math.round((tc * 0.75)  * 100) / 100,
      '7/8': Math.round((tc * 0.875) * 100) / 100,
      'TC'  : Math.round((tc * 1.0)   * 100) / 100,
    };
  }

  function renderPreview(base) {
    const vals = calcularDesdeTC(base);
    document.getElementById('pv_1_8').textContent = vals['1/8'] + ' h';
    document.getElementById('pv_1_4').textContent = vals['1/4'] + ' h';
    document.getElementById('pv_3_8').textContent = vals['3/8'] + ' h';
    document.getElementById('pv_1_2').textContent = vals['1/2'] + ' h';
    document.getElementById('pv_5_8').textContent = vals['5/8'] + ' h';
    document.getElementById('pv_3_4').textContent = vals['3/4'] + ' h';
    document.getElementById('pv_7_8').textContent = vals['7/8'] + ' h';
    document.getElementById('pv_tc').textContent  = vals['TC']  + ' h';
    tcDisplay.textContent = base;
  }

  // Mostrar nota si el tipo es TC
  function toggleNota() {
    if (tipoSelect.value === 'TC') notaPropagacion.classList.remove('hidden');
    else notaPropagacion.classList.add('hidden');
  }

  // Inicializar preview con TC del servidor
  renderPreview(tcHorasServer);

  // Si el usuario cambia horas y el tipo es TC, usar ese valor para la preview en vivo
  horasInput.addEventListener('input', () => {
    const val = Number(horasInput.value) || tcHorasServer;
    if (tipoSelect.value === 'TC') renderPreview(val);
    else renderPreview(tcHorasServer);
  });

  tipoSelect.addEventListener('change', () => {
    toggleNota();
    // si el usuario selecciona TC, previsualizamos desde el valor actual del input
    if (tipoSelect.value === 'TC') renderPreview(Number(horasInput.value) || tcHorasServer);
    else renderPreview(tcHorasServer);
  });

  // ejecutar al cargar
  toggleNota();
});
</script>
@endsection
