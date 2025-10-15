@extends('layout')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Registrar horario</h2>

    <form action="{{ route('horarios.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="tipo" class="block text-sm font-semibold text-gray-700">Tipo de institución</label>
            <select id="tipo" name="tipo"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="ucr">UCR</option>
                <option value="externo">Otra institución pública o privada</option>
            </select>
        </div>

        <div>
            <label for="dia" class="block text-sm font-semibold text-gray-700">Día</label>
            <select id="dia" name="dia"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="Lunes">Lunes</option>
                <option value="Martes">Martes</option>
                <option value="Miércoles">Miércoles</option>
                <option value="Jueves">Jueves</option>
                <option value="Viernes">Viernes</option>
                <option value="Sábado">Sábado</option>
            </select>
        </div>

        <div>
            <label for="hora_inicio" class="block text-sm font-semibold text-gray-700">Hora de inicio</label>
            <input type="time" id="hora_inicio" name="hora_inicio"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label for="hora_fin" class="block text-sm font-semibold text-gray-700">Hora de fin</label>
            <input type="time" id="hora_fin" name="hora_fin"
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md transition">
                Guardar horario
            </button>
        </div>
    </form>
</div>
@endsection
