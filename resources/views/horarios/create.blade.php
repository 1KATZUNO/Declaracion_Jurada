@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Registrar Horarios (plantilla UCR)</h1>
            <a href="{{ route('horarios.index') }}" class="text-sm text-white underline">Volver a Horarios</a>
        </div>

        <form action="{{ route('horarios.store') }}" method="POST" class="p-6" id="multiHorarioForm">
            @csrf

            {{-- ya no pedimos id_declaracion aquí --}}
            <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jornada</label>
                    <select name="id_jornada" id="id_jornada" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Seleccione jornada...</option>
                        @foreach($jornadas as $j)
                            <option value="{{ $j->id_jornada }}" data-horas="{{ $j->horas_por_semana }}">{{ $j->tipo }} — {{ $j->horas_por_semana }}h</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-1 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Estado horas</label>
                    <div id="contadorHoras" class="mt-1 px-3 py-2 rounded-md text-sm font-semibold">
                        <span id="targetHoras">Objetivo: — h</span>
                        <span class="mx-2">·</span>
                        <span id="consumidasHoras">Consumidas: 0 h</span>
                        <span class="mx-2">·</span>
                        <span id="restantesHoras">Restantes: — h</span>
                    </div>
                </div>
            </div>

            <div id="filasContainer" class="space-y-3">
                <!-- Fila inicial (tipo siempre UCR, no lugar) -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end fila-horario border border-gray-200 rounded p-3 bg-gray-50">
                    {{-- Forzar tipo UCR en cada fila (input oculto) --}}
                    <input type="hidden" name="tipo[]" value="ucr">
                    <div>
                        <label class="text-xs text-gray-600">Día</label>
                        <select name="dia[]" class="w-full px-2 py-1 border rounded">
                            <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                            <option>Jueves</option><option>Viernes</option><option>Sábado</option><option>Domingo</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Hora inicio</label>
                        <input type="time" name="hora_inicio[]" class="w-full px-2 py-1 border rounded">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">Hora fin</label>
                        <input type="time" name="hora_fin[]" class="w-full px-2 py-1 border rounded">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="btnRemove inline-flex items-center px-3 py-1 text-xs text-red-700 bg-red-50 border border-red-300 rounded">Eliminar</button>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="button" id="addFila" class="px-4 py-2 text-sm bg-white border rounded hover:bg-gray-50">Añadir fila</button>
                <button type="submit" id="submitBtn" class="px-4 py-2 text-sm bg-blue-600 text-white rounded">Guardar horarios</button>
            </div>

            <div class="mt-3 text-sm text-red-600" id="errorServer">
                @error('horarios') {{ $message }} @enderror
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const idJornadaSel = document.getElementById('id_jornada');
    const filasContainer = document.getElementById('filasContainer');
    const addFila = document.getElementById('addFila');
    const contador = document.getElementById('contadorHoras');
    const targetHorasEl = document.getElementById('targetHoras');
    const consumidasEl = document.getElementById('consumidasHoras');
    const restantesEl = document.getElementById('restantesHoras');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('multiHorarioForm');

    function parseTimeToMinutes(h){
        if(!h) return 0;
        const parts = h.split(':').map(Number);
        return parts[0]*60 + (parts[1]||0);
    }

    function calcularConsumidas(){
        let total = 0;
        filasContainer.querySelectorAll('.fila-horario').forEach(row => {
            const hi = row.querySelector('input[name="hora_inicio[]"]').value;
            const hf = row.querySelector('input[name="hora_fin[]"]').value;
            if (hi && hf) {
                const s = parseTimeToMinutes(hi);
                const e = parseTimeToMinutes(hf);
                if (e > s) total += (e - s);
            }
        });
        return total;
    }

    function actualizarContador(){
        const opt = idJornadaSel.selectedOptions[0];
        const target = opt ? Number(opt.dataset.horas || 0) : 0;
        const targetMin = target * 60;
        const consumidas = calcularConsumidas();
        const restantes = targetMin - consumidas;

        targetHorasEl.textContent = `Objetivo: ${target} h`;
        consumidasEl.textContent = `Consumidas: ${ (consumidas/60).toFixed(2) } h`;
        restantesEl.textContent = `Restantes: ${ (restantes/60).toFixed(2) } h`;

        contador.classList.remove('bg-red-100','bg-yellow-100','bg-green-100','text-red-700','text-yellow-700','text-green-700','border','border-red-300','border-yellow-300','border-green-300');
        if (restantes === 0) {
            contador.classList.add('bg-green-100','text-green-700','border','border-green-300');
        } else if (restantes > 0) {
            contador.classList.add('bg-yellow-100','text-yellow-700','border','border-yellow-300');
        } else {
            contador.classList.add('bg-red-100','text-red-700','border','border-red-300');
        }

        submitBtn.disabled = (idJornadaSel.value === '') || (restantes !== 0);
    }

    filasContainer.addEventListener('input', (e) => {
        if (e.target.matches('input[name="hora_inicio[]"], input[name="hora_fin[]"]')) {
            actualizarContador();
        }
    });

    idJornadaSel.addEventListener('change', actualizarContador);

    addFila.addEventListener('click', () => {
        const template = filasContainer.querySelector('.fila-horario');
        const clone = template.cloneNode(true);
        // limpiar valores (mantener el input hidden tipo[] tal cual)
        clone.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        filasContainer.appendChild(clone);
        actualizarContador();
    });

    filasContainer.addEventListener('click', (e) => {
        if (e.target.matches('.btnRemove')) {
            const row = e.target.closest('.fila-horario');
            if (row && filasContainer.querySelectorAll('.fila-horario').length > 1) {
                row.remove();
                actualizarContador();
            }
        }
    });

    actualizarContador();

    form.addEventListener('submit', (e) => {
        const opt = idJornadaSel.selectedOptions[0];
        const target = opt ? Number(opt.dataset.horas || 0) : 0;
        const targetMin = target * 60;
        const consumidas = calcularConsumidas();
        if (idJornadaSel.value === '') {
            e.preventDefault();
            showToast('Seleccione una jornada.', 'warning');
            return;
        }
        if (consumidas !== targetMin) {
            e.preventDefault();
            showToast('La suma de los intervalos debe coincidir exactamente con las horas de la jornada seleccionada.', 'error');
            return;
        }
    });
});
</script>
@endsection
