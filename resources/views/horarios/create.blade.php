@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Registrar Horario</h1>
        </div>

        <!-- Form -->
        <form action="{{ route('horarios.store') }}" method="POST" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Tipo de institución -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de institución</label>
                    <select id="tipo" name="tipo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="ucr">UCR</option>
                        <option value="externo">Otra institución pública o privada</option>
                    </select>
                </div>

                <!-- Día -->
                <div>
                    <label for="dia" class="block text-sm font-medium text-gray-700 mb-2">Día</label>
                    <select id="dia" name="dia"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                    </select>
                </div>

                <!-- Hora de inicio -->
                <div>
                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora de inicio</label>
                    <input type="time" id="hora_inicio" name="hora_inicio"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <!-- Hora de fin -->
                <div>
                    <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Hora de fin</label>
                    <input type="time" id="hora_fin" name="hora_fin"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('horarios.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Guardar Horario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
