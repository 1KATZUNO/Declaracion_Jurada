@extends('layout')
@section('titulo', 'Editar Unidad Académica')
 @csrf
{{-- Oculta el menú lateral en esta vista --}}
@section('hide_sidebar', true)

@section('contenido')

@php
  $breadcrumbs = [
    'Inicio' => route('home'),
    'Unidades Académicas' => route('unidades.index'),
    'Editar' => null,
  ];
@endphp

<div class="max-w-5xl mx-auto bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
  {{-- Breadcrumb --}}
  <nav class="px-6 py-3 text-xs text-gray-600 bg-[#F0F2F5]" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-1">
      @foreach($breadcrumbs as $label => $url)
        @if ($url)
          <li><a href="{{ $url }}" class="hover:text-gray-800 hover:underline">{{ $label }}</a></li>
          <li class="text-gray-400">/</li>
        @else
          <li class="text-gray-800 font-medium" aria-current="page">{{ $label }}</li>
        @endif
      @endforeach
    </ol>
  </nav>

  {{-- Encabezado banda azul --}}
  <div class="bg-[#0B5ED7] px-6 md:px-8 py-5 md:py-6 flex items-center justify-between">
    <div class="min-w-0">
      <h2 class="text-xl md:text-2xl font-semibold text-white truncate">Editar Unidad Académica</h2>
      <p class="text-blue-100 text-sm mt-1">Modifique la información de la unidad.</p>
    </div>
    <a href="{{ route('unidades.index') }}"
       class="px-4 py-2 text-sm font-medium text-[#0B5ED7] bg-white rounded-md hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-white shadow-sm"
       aria-label="Volver al listado">
      ← Volver
    </a>
  </div>

  <div class="p-6 md:p-8 space-y-8">
    {{-- Resumen de errores --}}
    @if ($errors->any())
      <div class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="status" aria-live="polite">
        <p class="font-medium mb-1">Por favor corrija los siguientes campos:</p>
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('unidades.update', $unidad->id_unidad) }}" class="space-y-8" novalidate>
      @csrf
      @method('PUT')

      {{-- Sección: Datos de la unidad --}}
      <section aria-labelledby="sec-datos-unidad" class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <h3 id="sec-datos-unidad" class="text-base font-semibold text-gray-900">Datos de la unidad</h3>
          <p class="text-sm text-gray-500 mt-1">Actualice la información institucional.</p>
        </div>

        <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- Nombre --}}
          <div class="md:col-span-2">
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
              value="{{ old('nombre', $unidad->nombre) }}"
              maxlength="100"
              placeholder="Ej.: Escuela de Ciencias de la Computación e Informática"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-md bg-gray-50 hover:bg-white focus:ring-2 focus:ring-[#0B5ED7] focus:border-[#0B5ED7] @error('nombre') border-red-500 ring-red-200 @enderror"
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
          <div class="md:col-span-1">
            <label for="id_sede" class="block text-sm font-medium text-gray-700 mb-2">Sede asociada</label>
            <select
              id="id_sede"
              name="id_sede"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-md bg-gray-50 hover:bg-white focus:ring-2 focus:ring-[#0B5ED7] focus:border-[#0B5ED7] @error('id_sede') border-red-500 ring-red-200 @enderror"
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

          <div class="md:col-span-1"></div>
        </div>
      </section>

      {{-- Acciones --}}
      <div class="flex flex-col md:flex-row md:justify-end gap-3 pt-2">
        <a href="{{ route('unidades.index') }}"
           class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
          Cancelar
        </a>
        <button type="submit"
                onclick="this.disabled=true; this.innerText='Actualizando…'; this.form.submit();"
                class="px-6 py-2.5 text-sm font-medium text-white bg-[#0B5ED7] border border-transparent rounded-md hover:bg-[#0A53C1] focus:outline-none focus:ring-2 focus:ring-[#0B5ED7] shadow-sm">
          Actualizar
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
