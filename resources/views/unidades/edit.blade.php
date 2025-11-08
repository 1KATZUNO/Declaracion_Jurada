@extends('layout')
@section('titulo', 'Editar Unidad Académica')

@section('contenido')
<div class="container mx-auto w-full max-w-5xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">EDITAR UNIDAD ACADÉMICA</h2>
            <p class="text-gray-500 text-sm mt-1">
                Modifique la información correspondiente a la unidad académica seleccionada.
            </p>
        </div>

        {{-- FORMULARIO --}}
        <form method="POST"
              action="{{ route('unidades.update', $unidad->id_unidad) }}"
              class="p-2 sm:p-4 md:p-8 space-y-8"
              novalidate>
            @csrf
            @method('PUT')

            {{-- DATOS DE LA UNIDAD --}}
            <section>
                <h3 class="text-xs font-semibold text-gray-600 tracking-wider uppercase">
                    Datos de la unidad
                </h3>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nombre de la unidad (ancho completo) --}}
                    <div class="md:col-span-2">
                        <label for="nombre"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la unidad académica o administrativa
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="nombre"
                            type="text"
                            name="nombre"
                            value="{{ old('nombre', $unidad->nombre) }}"
                            maxlength="100"
                            placeholder="Ej: Escuela de Matemática"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600
                                   transition-colors bg-gray-50 hover:bg-white @error('nombre') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            required
                        >
                        @error('nombre')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sede asociada --}}
                    <div>
                        <label for="id_sede"
                               class="block text-sm font-medium text-gray-700 mb-2">
                            Sede asociada
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="id_sede"
                            name="id_sede"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600
                                   transition-colors bg-gray-50 hover:bg-white @error('id_sede') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            required
                        >
                            @foreach($sedes as $s)
                                <option value="{{ $s->id_sede }}"
                                    @selected(old('id_sede', $unidad->id_sede) == $s->id_sede)>
                                    {{ $s->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_sede')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </section>

            {{-- BOTONES --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('unidades.index') }}"
                   class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium
                          text-gray-700 bg-white border border-gray-300 rounded-md
                          hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors">
                    Cancelar
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium
                               text-white bg-blue-700 rounded-md hover:bg-blue-800
                               focus:outline-none focus:ring-2 focus:ring-blue-600 shadow-sm transition-colors">
                    Actualizar Unidad
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
