@extends('layout')
@section('titulo', 'Nueva Sede')

@section('contenido')
<div class="max-w-5xl mx-auto px-4 py-8">

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">

        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">NUEVA SEDE</h2>
            <p class="text-gray-500 text-sm mt-1">Complete la información requerida para registrar una nueva sede universitaria.</p>
        </div>

        <form method="POST" action="{{ route('sedes.store') }}" class="p-8 space-y-8">
            @csrf

            <section>
                <h3 class="text-xs font-semibold text-gray-600 tracking-wider uppercase">Datos de la sede</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la sede <span class="text-red-500">*</span></label>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                                placeholder="Ej: Sede Guanacaste"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors bg-gray-50 hover:bg-white" required>
                        @error('nombre')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}"
                                placeholder="Ej: Liberia, Guanacaste"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-colors bg-gray-50 hover:bg-white">
                        @error('ubicacion')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('sedes.index') }}"
                    class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors">
                    Cancelar
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 shadow-sm transition-colors">
                    Guardar Sede
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
