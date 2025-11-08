@extends('layout')
@section('titulo', 'Editar Unidad Académica')

{{-- NO ocultamos el sidebar para mantener el layout del Figma --}}

@section('contenido')
<div class="max-w-5xl mx-auto bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">

  {{-- Encabezado estilo Figma  --}}
  <div class="px-6 md:px-8 py-6">
    <h1 class="text-[22px] md:text-2xl font-bold text-[#0B2C63] tracking-wide uppercase">
      Editar Unidad Académica
    </h1>
    <p class="text-sm text-gray-600 mt-2">Datos de la unidad</p>
  </div>

  <div class="px-6 md:px-8 pb-8">
    {{-- Resumen de errores --}}
    @if ($errors->any())
      <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="status" aria-live="polite">
        <p class="font-medium mb-1">Por favor corrija los siguientes campos:</p>
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('unidades.update', $unidad->id_unidad) }}" novalidate>
      @csrf
      @method('PUT')

      {{-- GRID EXACTA AL Figma: dos columnas iguales en desktop, una en móvil --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Campo: Nombre --}}
        <div class="flex flex-col">
          <label for="nombre" class="text-sm font-medium text-gray-700 mb-1">Nombre de la unidad académica o administrativa</label>
          <input
            id="nombre"
            name="nombre"
            type="text"
            maxlength="100"
            placeholder="Ej: Escuela de Matemática"
            value="{{ old('nombre', $unidad->nombre) }}"
            class="w-full px-4 py-2.5 border rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-[#0B5ED7] focus:border-[#0B5ED7] @error('nombre') border-red-500 ring-red-200 @enderror"
            required
          />
          @error('nombre')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Campo: Sede asociada--}}
        <div class="flex flex-col">
          <label for="id_sede" class="text-sm font-medium text-gray-700 mb-1">Sede Asociada</label>
          {{-- mi UX requiere select conectado a BD, déjarlo como <select>. 
                --}}
          <select
            id="id_sede"
            name="id_sede"
            class="w-full px-4 py-2.5 border rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-[#0B5ED7] focus:border-[#0B5ED7] @error('id_sede') border-red-500 ring-red-200 @enderror"
            required
          >
            @foreach($sedes as $s)
              <option value="{{ $s->id_sede }}" @selected(old('id_sede', $unidad->id_sede) == $s->id_sede)>{{ $s->nombre }}</option>
            @endforeach
          </select>
          @error('id_sede')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

      </div>

      {{-- Botones alineados como en el Figma --}}
      <div class="flex items-center gap-3 pt-8">
        <a href="{{ route('unidades.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
          Cancelar
        </a>
        <button type="submit"
                onclick="this.disabled=true; this.innerText='Guardando…'; this.form.submit();"
                class="px-5 py-2.5 text-sm font-medium text-white bg-[#0B2C63] rounded-md hover:opacity-90">
          Guardar
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
