@extends('layout')

@section('content')
<div class="max-w-5xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Crear nueva declaración jurada</h2>

    <form action="{{ route('declaraciones.store') }}" method="POST" id="declaracionForm" class="space-y-6">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="id_usuario" class="block text-sm font-semibold text-gray-700">Usuario</label>
                <input type="number" name="id_usuario" id="id_usuario"
                       class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label for="id_formulario" class="block text-sm font-semibold text-gray-700">Formulario</label>
                <select name="id_formulario" id="id_formulario"
                        class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    @foreach($formularios as $f)
                        <option value="{{ $f->id_formulario }}">{{ $f->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="id_unidad" class="block text-sm font-semibold text-gray-700">Unidad académica</label>
                <select name="id_unidad" id="id_unidad"
                        class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    @foreach($unidades as $u)
                        <option value="{{ $u->id_unidad }}">{{ $u->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="id_cargo" class="block text-sm font-semibold text-gray-700">Cargo</label>
                <select name="id_cargo" id="id_cargo"
                        class="w-full border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500" required>
                    @foreach($cargos as $c)
                        <option value="{{ $c->id_cargo }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="fecha_desde" class="block text-sm font-semibold text-gray-700">Fecha desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde"
                       class="w-full border-gray-300 rounded-lg p-2" required>
            </div>

            <div>
                <label for="fecha_hasta" class="block text-sm font-semibold text-gray-700">Fecha hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta"
                       class="w-full border-gray-300 rounded-lg p-2" required>
            </div>

            <div>
                <label for="horas_totales" class="block text-sm font-semibold text-gray-700">Horas totales</label>
                <input type="number" step="0.1" name="horas_totales" id="horas_totales"
                       class="w-full border-gray-300 rounded-lg p-2" required>
            </div>
        </div>

        <hr class="my-6">

        <h3 class="text-lg font-semibold text-gray-800 mb-2">Horarios</h3>

        <div id="horarios-container" class="space-y-4">
            <div class="border p-4 rounded-lg bg-gray-50">
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Tipo</label>
                        <select name="tipo[]" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="ucr">UCR</option>
                            <option value="externo">Otra institución</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Día</label>
                        <select name="dia[]" class="border-gray-300 rounded-lg p-2 w-full">
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Inicio</label>
                        <input type="time" name="hora_inicio[]" class="border-gray-300 rounded-lg p-2 w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Fin</label>
                        <input type="time" name="hora_fin[]" class="border-gray-300 rounded-lg p-2 w-full">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-4">
            <button type="button" id="add-horario"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition">
                + Añadir otro horario
            </button>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow-md transition">
                Guardar declaración
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('add-horario').addEventListener('click', () => {
    const container = document.getElementById('horarios-container');
    const clone = container.children[0].cloneNode(true);
    clone.querySelectorAll('input, select').forEach(el => el.value = '');
    container.appendChild(clone);
});
</script>
@endsection
