@extends('layout')
 @csrf
@section('content')
<div class="container mx-auto w-full max-w-6xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Editar declaración jurada</h2>
            <p class="text-blue-100 text-sm mt-1">Modifique los campos necesarios</p>
        </div>

        <form action="{{ route('declaraciones.update', $d->id_declaracion) }}" method="POST" class="p-2 sm:p-4 md:p-8">
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
                               value="{{ $d->horas_totales }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-md bg-gray-100" readonly>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="rounded-md border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    La gestión completa de horarios se hace desde el módulo <strong>Horarios</strong>.
                </div>
            </div>

            <div class="mb-6">
                <label for="id_horario" class="block text-sm font-medium text-gray-700 mb-2">Horario asociado</label>
                <select name="id_horario" id="id_horario" class="w-full px-4 py-2.5 border border-gray-300 rounded-md bg-white">
                    <option value="">-- Ninguno --</option>
                    @foreach(($horarios ?? []) as $h)
                        <option value="{{ $h->id_horario }}"
                            data-horas="{{ optional($h->jornada)->horas_por_semana ?? '' }}"
                            @selected( in_array($h->id_horario, $d->horarios->pluck('id_horario')->toArray()) )>
                            Horario #{{ $h->id_horario }} — {{ $h->dia }} {{ \Illuminate\Support\Str::substr($h->hora_inicio,0,5) }}‑{{ \Illuminate\Support\Str::substr($h->hora_fin,0,5) }} @if($h->jornada) ({{ $h->jornada->tipo }} {{ $h->jornada->horas_por_semana }}h) @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Al cambiar el horario, el sistema actualizará la asociación del horario seleccionado al guardar.</p>
            </div>

            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
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
document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('id_horario');
    const horasInput = document.getElementById('horas_totales');
    if (!sel) return;
    sel.addEventListener('change', () => {
        const opt = sel.selectedOptions[0];
        const h = opt ? opt.dataset.horas || '' : '';
        horasInput.value = h ? Number(h) : '';
    });

    // inicializar según selección actual
    const initOpt = sel.selectedOptions[0];
    if (initOpt) {
        const h = initOpt.dataset.horas || '';
        if (h) document.getElementById('horas_totales').value = Number(h);
    }
});
</script>
@endsection
