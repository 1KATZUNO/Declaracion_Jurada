@extends('layout')
 @csrf
@section('content')
<div class="container mx-auto w-full max-w-6xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
            <h2 class="text-2xl font-semibold text-white">Crear nueva declaración jurada</h2>
            <p class="text-blue-100 text-sm mt-1">Complete el formulario con la información requerida</p>
        </div>

        <form action="{{ route('declaraciones.store') }}" method="POST" id="declaracionForm" class="p-2 sm:p-4 md:p-8">
            @csrf

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Información general</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="id_usuario" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <select name="id_usuario" id="id_usuario" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white">
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id_usuario }}">{{ $u->nombre }} {{ $u->apellido }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_formulario" class="block text-sm font-medium text-gray-700 mb-2">Formulario</label>
                        <select name="id_formulario" id="id_formulario"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                            @foreach($formularios as $f)
                                <option value="{{ $f->id_formulario }}">{{ $f->titulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_unidad" class="block text-sm font-medium text-gray-700 mb-2">Unidad académica</label>
                        <select name="id_unidad" id="id_unidad"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                            @foreach($unidades as $u)
                                <option value="{{ $u->id_unidad }}">{{ $u->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_cargo" class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                        <select name="id_cargo" id="id_cargo"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                            @foreach($cargos as $c)
                                <option value="{{ $c->id_cargo }}">{{ $c->nombre }}</option>
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
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                    </div>

                    <div>
                        <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-2">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                    </div>

                    <div>
                        <label for="horas_totales" class="block text-sm font-medium text-gray-700 mb-2">Horas totales</label>
                        <input type="number" step="0.1" name="horas_totales" id="horas_totales"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md bg-gray-100 text-gray-700"
                               readonly>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios en otras instituciones</h3>
                <div id="horariosExternos" class="space-y-4">
                    <div class="fila-horario-externo bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Institución</label>
                                <input type="text" name="ext_institucion[]" class="mt-1 w-full rounded border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Día</label>
                                <select name="ext_dia[]" class="mt-1 w-full rounded border-gray-300">
                                    <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                                    <option>Jueves</option><option>Viernes</option><option>Sábado</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Inicio</label>
                                    <input type="time" name="ext_hora_inicio[]" class="mt-1 w-full rounded border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fin</label>
                                    <input type="time" name="ext_hora_fin[]" class="mt-1 w-full rounded border-gray-300">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addHorarioExterno" class="mt-4 px-4 py-2 text-sm bg-gray-100 rounded hover:bg-gray-200">
                    + Agregar otro horario externo
                </button>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios UCR</h3>
                
                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jornada UCR</label>
                        <select name="id_jornada" id="id_jornada" required class="mt-1 w-full rounded border-gray-300">
                            <option value="">Seleccione jornada...</option>
                            @foreach($jornadas as $j)
                                <option value="{{ $j->id_jornada }}" data-horas="{{ $j->horas_por_semana }}">
                                    {{ $j->tipo }} — {{ $j->horas_por_semana }}h
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado horas</label>
                        <div id="contadorHoras" class="mt-1 px-3 py-2 rounded text-sm font-medium bg-gray-50">
                            Objetivo: <span id="targetHoras">—</span> h · 
                            Asignadas: <span id="horasAsignadas">0</span> h ·
                            Restantes: <span id="horasRestantes">—</span> h
                        </div>
                    </div>
                </div>

                <div id="horariosUCR" class="space-y-4">
                    <!-- Template inicial para horarios UCR -->
                    <div class="fila-horario bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Día</label>
                                <select name="ucr_dia[]" class="mt-1 w-full rounded border-gray-300">
                                    <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                                    <option>Jueves</option><option>Viernes</option><option>Sábado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hora inicio</label>
                                <input type="time" name="ucr_hora_inicio[]" class="mt-1 w-full rounded border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hora fin</label>
                                <input type="time" name="ucr_hora_fin[]" class="mt-1 w-full rounded border-gray-300">
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="btn-remove-ucr px-3 py-2 text-sm text-red-600 hover:text-red-700">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="addHorarioUCR" class="mt-4 px-4 py-2 text-sm bg-blue-100 rounded hover:bg-blue-200">
                    + Agregar horario UCR
                </button>
            </div>

            <div class="flex flex-col md:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('declaraciones.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-colors">
                    Guardar declaración
                </button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selJornada = document.getElementById('id_jornada');
    const horasInput = document.getElementById('horas_totales');
    const targetHoras = document.getElementById('targetHoras');
    const horasAsignadas = document.getElementById('horasAsignadas');
    const horasRestantes = document.getElementById('horasRestantes');
    const horariosUCR = document.getElementById('horariosUCR');
    const form = document.getElementById('declaracionForm');

    // Template para nuevo horario UCR
    const addHorarioUCR = document.getElementById('addHorarioUCR');
    const horariosUCRContainer = document.getElementById('horariosUCR');
    const templateHorarioUCR = horariosUCRContainer.querySelector('.fila-horario').cloneNode(true);
    
    function parseTimeToMinutes(timeStr) {
        if (!timeStr) return 0;
        const [hours, minutes] = timeStr.split(':').map(Number);
        return (hours * 60) + minutes;
    }

    // Nueva función: verifica si hay conflictos entre horarios
    function verificarConflictos() {
        const horarios = [];
        
        // Recolectar horarios externos
        document.querySelectorAll('.fila-horario-externo').forEach(row => {
            const dia = row.querySelector('select[name="ext_dia[]"]').value;
            const inicio = row.querySelector('input[name="ext_hora_inicio[]"]').value;
            const fin = row.querySelector('input[name="ext_hora_fin[]"]').value;
            if (dia && inicio && fin) {
                horarios.push({ tipo: 'externo', dia, inicio, fin });
            }
        });

        // Recolectar horarios UCR
        document.querySelectorAll('.fila-horario').forEach(row => {
            const dia = row.querySelector('select[name="ucr_dia[]"]').value;
            const inicio = row.querySelector('input[name="ucr_hora_inicio[]"]').value;
            const fin = row.querySelector('input[name="ucr_hora_fin[]"]').value;
            if (dia && inicio && fin) {
                horarios.push({ tipo: 'ucr', dia, inicio, fin });
            }
        });

        // Verificar conflictos
        for (let i = 0; i < horarios.length; i++) {
            for (let j = i + 1; j < horarios.length; j++) {
                const h1 = horarios[i];
                const h2 = horarios[j];

                if (h1.dia !== h2.dia) continue;

                const inicio1 = parseTimeToMinutes(h1.inicio);
                const fin1 = parseTimeToMinutes(h1.fin);
                const inicio2 = parseTimeToMinutes(h2.inicio);
                const fin2 = parseTimeToMinutes(h2.fin);

                // Verificar solapamiento
                if (inicio1 < fin2 && fin1 > inicio2) {
                    return `Conflicto detectado en ${h1.dia}: los horarios se solapan`;
                }

                // Si uno es UCR y otro externo, verificar hora de diferencia
                if (h1.tipo !== h2.tipo) {
                    const minDiff = Math.min(
                        Math.abs(fin1 - inicio2),
                        Math.abs(fin2 - inicio1)
                    );
                    if (minDiff < 60) {
                        return `Debe haber al menos 1 hora entre horario UCR y externo en ${h1.dia}`;
                    }
                }
            }
        }

        return null;
    }

    function calcularHorasAsignadas() {
        let totalMinutos = 0;
        const filas = horariosUCR.querySelectorAll('.fila-horario');
        
        filas.forEach(fila => {
            const inicio = fila.querySelector('input[name="ucr_hora_inicio[]"]').value;
            const fin = fila.querySelector('input[name="ucr_hora_fin[]"]').value;
            
            if (inicio && fin) {
                const minutos = parseTimeToMinutes(fin) - parseTimeToMinutes(inicio);
                if (minutos > 0) totalMinutos += minutos;
            }
        });

        return totalMinutos / 60; // convertir a horas
    }

    function actualizarContador() {
        const jornada = selJornada.options[selJornada.selectedIndex];
        const horasObjetivo = jornada ? Number(jornada.dataset.horas) : 0;

        // Actualizar horas totales directamente desde la jornada
        horasInput.value = horasObjetivo;
        
        const horasAsignadasVal = calcularHorasAsignadas();
        const horasRestantesVal = horasObjetivo - horasAsignadasVal;

        targetHoras.textContent = horasObjetivo;
        horasAsignadas.textContent = horasAsignadasVal.toFixed(1);
        horasRestantes.textContent = horasRestantesVal.toFixed(1);
    }

    // Función para validar horario
    function validarHorario(horaInicio, horaFin) {
        const inicio = horaInicio.split(':').map(Number);
        const fin = horaFin.split(':').map(Number);
        const inicioMinutos = inicio[0] * 60 + inicio[1];
        const finMinutos = fin[0] * 60 + fin[1];

        // Validar rango general (7:00 - 21:00)
        if (inicioMinutos < 7 * 60) {
            return 'No se pueden programar clases antes de las 7:00 AM';
        }
        if (finMinutos > 21 * 60) {
            return 'No se pueden programar clases después de las 21:00 (9:00 PM)';
        }

        // Validar hora de almuerzo (12:00 - 13:00)
        if ((inicioMinutos >= 12 * 60 && inicioMinutos < 13 * 60) || 
            (finMinutos > 12 * 60 && finMinutos <= 13 * 60)) {
            return 'No se pueden programar clases entre 12:00 PM y 1:00 PM (hora de almuerzo)';
        }

        return null; // null significa que no hay error
    }

    // Event Listeners
    form.addEventListener('submit', (e) => {
        const conflicto = verificarConflictos();
        if (conflicto) {
            e.preventDefault();
            alert(conflicto);
            return;
        }

        const jornada = selJornada.options[selJornada.selectedIndex];
        const horasObjetivo = jornada ? Number(jornada.dataset.horas) : 0;
        const horasAsignadasVal = calcularHorasAsignadas();

        if (horasAsignadasVal === 0) {
            e.preventDefault();
            alert('Debe asignar horarios para completar la jornada');
            return;
        }

        if (horasAsignadasVal !== horasObjetivo) {
            e.preventDefault();
            alert('Las horas asignadas deben coincidir exactamente con las horas de la jornada');
            return;
        }
    });

    selJornada.addEventListener('change', actualizarContador);
    horariosUCR.addEventListener('input', (e) => {
        if (e.target.matches('input[type="time"]')) {
            actualizarContador();
        }
    });

    // Agregar horario UCR
    addHorarioUCR.addEventListener('click', () => {
        const nuevo = templateHorarioUCR.cloneNode(true);
        // Limpiar valores
        nuevo.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
        nuevo.querySelector('select').selectedIndex = 0;
        horariosUCRContainer.appendChild(nuevo);
        actualizarContador();
    });

    // Validar horarios al cambiar
    horariosUCRContainer.addEventListener('change', (e) => {
        if (e.target.matches('input[type="time"]')) {
            const fila = e.target.closest('.fila-horario');
            const inicio = fila.querySelector('input[name="ucr_hora_inicio[]"]').value;
            const fin = fila.querySelector('input[name="ucr_hora_fin[]"]').value;

            if (inicio && fin) {
                const error = validarHorario(inicio, fin);
                if (error) {
                    alert(error);
                    e.target.value = ''; // limpiar el campo que causó el error
                    return;
                }
            }
            actualizarContador();
        }
    });

    // Eliminar horario UCR
    horariosUCRContainer.addEventListener('click', (e) => {
        if (e.target.matches('.btn-remove-ucr')) {
            const filas = horariosUCRContainer.querySelectorAll('.fila-horario');
            if (filas.length > 1) {
                e.target.closest('.fila-horario').remove();
                actualizarContador();
            }
        }
    });

    // Inicializar contador
    actualizarContador();
});
</script>
@endsection
