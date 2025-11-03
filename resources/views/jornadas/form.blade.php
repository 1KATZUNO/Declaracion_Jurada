@extends('layout')

@section('titulo', ($mode === 'create' ? 'Nueva Jornada' : 'Editar Jornada'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
  <div class="bg-white/90 backdrop-blur border border-gray-200 rounded-2xl shadow-[0_10px_30px_rgba(2,6,23,0.06)] overflow-hidden">
    <div class="relative px-8 py-7 bg-gradient-to-r from-blue-600 via-blue-600 to-indigo-600">
      <h2 class="text-2xl font-semibold text-white tracking-tight">
        {{ $mode === 'create' ? 'Nueva Jornada' : 'Editar Jornada' }}
      </h2>
      <p class="text-blue-100 text-sm mt-1">Seleccione el tipo y se completarán las horas automáticamente</p>
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
            <select id="tipoSelect" name="tipo"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white"
                    required>
              <option value="" disabled {{ old('tipo', $jornada->tipo) ? '' : 'selected' }}>Seleccione...</option>
              <option value="1/8" {{ old('tipo', $jornada->tipo) === '1/8' ? 'selected' : '' }}>1/8</option>
              <option value="1/4" {{ old('tipo', $jornada->tipo) === '1/4' ? 'selected' : '' }}>1/4</option>
              <option value="1/2" {{ old('tipo', $jornada->tipo) === '1/2' ? 'selected' : '' }}>1/2</option>
              <option value="3/4" {{ old('tipo', $jornada->tipo) === '3/4' ? 'selected' : '' }}>3/4</option>
              <option value="TC" {{ old('tipo', $jornada->tipo) === 'TC' ? 'selected' : '' }}>Tiempo completo (TC)</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Las horas se completan según el tipo seleccionado.</p>
          </div>

          {{-- Horas por semana (solo lectura) --}}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Horas por semana</label>
            <input type="number"
                   id="horasInput"
                   name="horas_por_semana"
                   value="{{ old('horas_por_semana', $jornada->horas_por_semana) }}"
                   readonly
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700 focus:ring-0 focus:border-gray-300">
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-8">
          <a href="{{ route('jornadas.index') }}"
             class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
            Cancelar
          </a>
          <button type="submit"
                  class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
            {{ $mode === 'create' ? 'Crear jornada' : 'Guardar cambios' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Script para autocompletar las horas --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tipoSelect = document.getElementById('tipoSelect');
  const horasInput = document.getElementById('horasInput');

  // Mapeo fijo de equivalencias UCR
  const horasPorTipo = {
    '1/8': 5,
    '1/4': 10,
    '1/2': 20,
    '3/4': 30,
    'TC': 40
  };

  function actualizarHoras() {
    const tipo = tipoSelect.value;
    horasInput.value = horasPorTipo[tipo] ?? '';
  }

  tipoSelect.addEventListener('change', actualizarHoras);

  // Ejecutar al cargar (para editar)
  actualizarHoras();
});
</script>
@endsection
