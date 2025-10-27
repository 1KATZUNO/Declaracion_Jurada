@extends('layout')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Editar declaración jurada</h2>
            <p class="text-blue-100 text-sm mt-1">Modifique los campos necesarios</p>
        </div>

        <form action="{{ route('declaraciones.update', $d->id_declaracion) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información general</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <select name="id_usuario" id="id_usuario"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id_usuario }}" {{ $u->id_usuario == $d->id_usuario ? 'selected' : '' }}>
                                    {{ $u->nombre }} {{ $u->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_formulario" class="block text-sm font-medium text-gray-700 mb-2">Formulario</label>
                        <select name="id_formulario" id="id_formulario"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                            @foreach($formularios as $f)
                                <option value="{{ $f->id_formulario }}" {{ $f->id_formulario == $d->id_formulario ? 'selected' : '' }}>
                                    {{ $f->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_unidad" class="block text-sm font-medium text-gray-700 mb-2">Unidad académica</label>
                        <select name="id_unidad" id="id_unidad"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                            @foreach($unidades as $u)
                                <option value="{{ $u->id_unidad }}" {{ $u->id_unidad == $d->id_unidad ? 'selected' : '' }}>
                                    {{ $u->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_cargo" class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                        <select name="id_cargo" id="id_cargo"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                            @foreach($cargos as $c)
                                <option value="{{ $c->id_cargo }}" {{ $c->id_cargo == $d->id_cargo ? 'selected' : '' }}>
                                    {{ $c->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Período y horas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-2">Fecha desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde"
                               value="{{ $d->fecha_desde }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                    </div>

                    <div>
                        <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-2">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta"
                               value="{{ $d->fecha_hasta }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                    </div>

                    <div>
                        <label for="horas_totales" class="block text-sm font-medium text-gray-700 mb-2">Horas totales</label>
                        <input type="number" step="0.1" name="horas_totales" id="horas_totales"
                               value="{{ $d->horas_totales }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios</h3>

                <div id="horarios-container" class="space-y-4">
                    @foreach($d->horarios as $h)
                        <div class="border border-gray-300 rounded-lg p-5 bg-gray-50 hover:bg-white transition-colors horario-block">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                                    <select name="tipo[]" class="tipo-select w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                                        <option value="ucr" {{ $h->tipo === 'ucr' ? 'selected' : '' }}>UCR</option>
                                        <option value="externo" {{ $h->tipo === 'externo' ? 'selected' : '' }}>Otra institución</option>
                                    </select>
                                </div>

                                <div class="lugar-wrapper {{ $h->tipo === 'externo' ? '' : 'hidden' }}">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Lugar / Institución</label>
                                    <input type="text" name="lugar[]" value="{{ $h->lugar ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-md ..." placeholder="Nombre de la institución (solo para externo)">
                                </div>

                                <div class="dia-wrapper">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Día</label>
                                    <select name="dia[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                                        @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $dia)
                                            <option value="{{ $dia }}" {{ $h->dia === $dia ? 'selected' : '' }}>{{ $dia }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora inicio</label>
                                    <input type="time" name="hora_inicio[]" value="{{ $h->hora_inicio }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora fin</label>
                                    <input type="time" name="hora_fin[]" value="{{ $h->hora_fin }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="button" id="add-horario"
                            class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Añadir nuevo horario
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('declaraciones.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                   Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function setTipoBehavior(block){
    const tipo = block.querySelector('.tipo-select');
    const lugarWr = block.querySelector('.lugar-wrapper');

    const toggle = () => {
        if(tipo.value === 'externo'){
            lugarWr.classList.remove('hidden');
        } else {
            lugarWr.classList.add('hidden');
        }
    };
    tipo.addEventListener('change', toggle);
    toggle();
}

document.querySelectorAll('#horarios-container .horario-block').forEach(setTipoBehavior);

document.getElementById('add-horario').addEventListener('click', () => {
    const container = document.getElementById('horarios-container');
    const block = document.createElement('div');
    block.classList.add('border','border-gray-300','rounded-lg','p-5','bg-gray-50','hover:bg-white','transition-colors','mt-4','horario-block');
    block.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select name="tipo[]" class="tipo-select w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                    <option value="ucr">UCR</option>
                    <option value="externo">Otra institución</option>
                </select>
            </div>
            <div class="lugar-wrapper hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lugar / Institución</label>
                <input type="text" name="lugar[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-md ..." placeholder="Nombre de la institución (solo para externo)">
            </div>
            <div class="dia-wrapper">
                <label class="block text-sm font-medium text-gray-700 mb-2">Día</label>
                <select name="dia[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hora inicio</label>
                <input type="time" name="hora_inicio[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hora fin</label>
                <input type="time" name="hora_fin[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
            </div>
        </div>
    `;
    container.appendChild(block);
    setTipoBehavior(block);
});
</script>
@endsection
