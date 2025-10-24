@extends('layout')
@section('titulo', 'Nueva Unidad Académica')

@section('contenido')
<div class="max-w-4xl mx-auto px-4 py-8">
  <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
      <h2 class="text-2xl font-semibold text-white">Registrar Unidad Académica</h2>
      <p class="text-blue-100 text-sm mt-1">Complete la información de la nueva unidad</p>
    </div>

    <form method="POST" action="{{ route('unidades.store') }}" class="p-8" novalidate>
      @csrf

      {{-- Errores generales --}}
      @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
          <p class="font-medium mb-1">Por favor corrija los siguientes campos:</p>
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="space-y-6">
        {{-- Nombre --}}
        <div>
          <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
            Nombre de la unidad académica o administrativa
          </label>
          <p class="text-xs text-gray-500 mb-2">
            Ejemplos: “Escuela de Ciencias de la Computación e Informática”, “Facultad de Educación”, “Escuela de Matemática”.
          </p>
          <input
            id="nombre"
            type="text"
            name="nombre"
            value="{{ old('nombre') }}"
            maxlength="100"
            placeholder="Ej.: Escuela de Ciencias de la Computación e Informática"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white @error('nombre') border-red-500 ring-red-200 @enderror"
            required
            aria-describedby="ayuda-nombre"
          />
          @error('nombre')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
          <p id="ayuda-nombre" class="mt-1 text-xs text-gray-500">
            Este nombre aparecerá en la Declaración Jurada del funcionario.
          </p>
        </div>

        {{-- Sede --}}
        <div>
          <label for="id_sede" class="block text-sm font-medium text-gray-700 mb-2">Sede asociada</label>
          <select
            id="id_sede"
            name="id_sede"
            class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white @error('id_sede') border-red-500 ring-red-200 @enderror"
            required
          >
            <option value="">Seleccione una sede...</option>
            @foreach($sedes as $s)
              <option value="{{ $s->id_sede }}" @selected(old('id_sede') == $s->id_sede)>{{ $s->nombre }}</option>
            @endforeach
          </select>
          @error('id_sede')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
        <a href="{{ route('unidades.index') }}"
           class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
          Cancelar
        </a>
        <button type="submit"
                class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
          Guardar
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
