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
                        <label for="usuario_display" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <input type="text" 
                               value="{{ $nombreUsuario ?: 'Usuario no identificado' }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700" 
                               readonly>
                        <input type="hidden" name="id_usuario" value="{{ $usuarioActual ? $usuarioActual->id_usuario : '' }}">
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
                        <label for="id_sede" class="block text-sm font-medium text-gray-700 mb-2">Sede</label>
                        <select name="id_sede" id="id_sede"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 hover:bg-white" required>
                            <option value="">Seleccione una sede...</option>
                            @foreach($sedes as $s)
                                <option value="{{ $s->id_sede }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_unidad" class="block text-sm font-medium text-gray-700 mb-2">Unidad académica</label>
                        <select name="id_unidad" id="id_unidad"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-100" disabled required>
                            <option value="">Primero seleccione una sede</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios UCR</h3>
                
                <!-- Contenedor para múltiples cargos UCR -->
                <div id="cargosUCR" class="space-y-6">
                    <!-- Template de cargo UCR -->
                    <div class="cargo-ucr-block border-2 border-blue-200 rounded-lg p-4 bg-blue-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cargo UCR</label>
                                <select name="ucr_cargo[]" class="ucr-cargo-select mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
                                    <option value="">Seleccione cargo...</option>
                                    @foreach($cargos as $c)
                                        <option value="{{ $c->id_cargo }}">{{ $c->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jornada de este cargo</label>
                                <select name="ucr_jornada[]" class="ucr-jornada-select mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
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
                                <div class="contador-horas-cargo mt-1 px-3 py-2 rounded text-sm font-medium bg-white">
                                    Obj: <span class="target-horas-ucr">—</span> h · 
                                    Asig: <span class="horas-asignadas-ucr">0</span> h ·
                                    Rest: <span class="horas-restantes-ucr">—</span> h
                                </div>
                            </div>
                        </div>

                        <!-- Fechas de vigencia para este cargo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 p-3 bg-white rounded-lg border">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                                <input type="date" name="ucr_cargo_fecha_desde[]" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white"
                                       placeholder="dd/mm/aaaa">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                                <input type="date" name="ucr_cargo_fecha_hasta[]" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white"
                                       placeholder="dd/mm/aaaa">
                            </div>
                        </div>

                        <!-- Horarios de este cargo -->
                        <div class="horarios-cargo space-y-3 mb-3" data-cargo-index="0">
                            <div class="fila-horario-ucr bg-gray-50 p-3 rounded-lg border">
                                <!-- Campo oculto para identificar a qué cargo pertenece este horario -->
                                <input type="hidden" name="ucr_cargo_index[]" value="0" class="cargo-index-field">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Día</label>
                                        <select name="ucr_dia[]" 
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white">
                                            <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                                            <option>Jueves</option><option>Viernes</option><option>Sábado</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio</label>
                                        <input type="time" name="ucr_hora_inicio[]" 
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fin</label>
                                        <input type="time" name="ucr_hora_fin[]" 
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="btn-remove-horario-ucr px-2 py-1 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <button type="button" class="btn-add-horario-cargo px-3 py-1.5 text-sm bg-blue-100 rounded hover:bg-blue-200">
                                + Agregar horario a este cargo
                            </button>
                            <button type="button" class="btn-remove-cargo px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded">
                                🗑️ Eliminar cargo
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" id="addCargoUCR" class="mt-4 px-4 py-2 text-sm font-medium bg-green-100 text-green-700 rounded hover:bg-green-200">
                    + Agregar otro cargo UCR
                </button>
            </div>

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Horarios en otras instituciones</h3>
                
                <!-- Contenedor para múltiples instituciones externas -->
                <div id="institucionesExternas" class="space-y-6">
                    <!-- Template de institución externa -->
                    <div class="institucion-externa-block border-2 border-gray-200 rounded-lg p-4 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Institución</label>
                                <input type="text" name="ext_institucion[]" 
                                       placeholder="Nombre de la institución"
                                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cargo en esta institución</label>
                                <input type="text" name="ext_cargo[]" 
                                       placeholder="Ej: Profesor, Coordinador..."
                                       class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jornada de esta institución</label>
                                <select name="ext_jornada[]" class="ext-jornada-select mt-1 w-full px-3 py-2 border border-gray-300 rounded-md bg-white">
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
                                <div class="contador-horas-institucion mt-1 px-3 py-2 rounded text-sm font-medium bg-gray-50">
                                    Obj: <span class="target-horas">—</span> h · 
                                    Asig: <span class="horas-asignadas">0</span> h ·
                                    Rest: <span class="horas-restantes">—</span> h
                                </div>
                            </div>
                        </div>

                        <!-- Fechas de vigencia para esta institución -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                                <input type="date" name="ext_inst_fecha_desde[]" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white"
                                       placeholder="dd/mm/aaaa">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                                <input type="date" name="ext_inst_fecha_hasta[]" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white"
                                       placeholder="dd/mm/aaaa">
                            </div>
                        </div>

                        <!-- Horarios de esta institución -->
                        <div class="horarios-institucion space-y-3 mb-3" data-inst-index="0">
                            <div class="fila-horario-externo bg-white p-3 rounded-lg border">
                                <!-- Campo oculto para identificar a qué institución pertenece este horario -->
                                <input type="hidden" name="ext_inst_index[]" value="0" class="inst-index-field">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Día</label>
                                        <select name="ext_dia[]" 
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white">
                                            <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                                            <option>Jueves</option><option>Viernes</option><option>Sábado</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio</label>
                                        <input type="time" name="ext_hora_inicio[]" 
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fin</label>
                                        <input type="time" name="ext_hora_fin[]" 
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="btn-remove-horario-externo px-2 py-1 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <button type="button" class="btn-add-horario-institucion px-3 py-1.5 text-sm bg-blue-100 rounded hover:bg-blue-200">
                                + Agregar horario a esta institución
                            </button>
                            <button type="button" class="btn-remove-institucion px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded">
                                🗑️ Eliminar institución
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" id="addInstitucionExterna" class="mt-4 px-4 py-2 text-sm font-medium bg-green-100 text-green-700 rounded hover:bg-green-200">
                    + Agregar otra institución externa
                </button>
            </div>

            <!-- Observaciones adicionales -->
            <div class="mb-8">
                <label for="observaciones_adicionales" class="block text-sm font-medium text-gray-700 mb-2">Observaciones adicionales</label>
                <textarea name="observaciones_adicionales" id="observaciones_adicionales" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50" placeholder="Ingrese observaciones adicionales aquí...">{{ old('observaciones_adicionales') }}</textarea>
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
    const form = document.getElementById('declaracionForm');

    // ========== MÚLTIPLES CARGOS UCR ==========
    const cargosUCRContainer = document.getElementById('cargosUCR');
    const addCargoUCR = document.getElementById('addCargoUCR');
    
    // Template para nuevo cargo UCR
    const templateCargoUCR = cargosUCRContainer.querySelector('.cargo-ucr-block').cloneNode(true);
    
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
        document.querySelectorAll('.fila-horario-ucr').forEach(row => {
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

    // Calcular horas asignadas para un cargo UCR específico
    function calcularHorasCargo(cargoBlock) {
        let totalMinutos = 0;
        const filas = cargoBlock.querySelectorAll('.fila-horario-ucr');
        
        filas.forEach(fila => {
            const inicio = fila.querySelector('input[name="ucr_hora_inicio[]"]').value;
            const fin = fila.querySelector('input[name="ucr_hora_fin[]"]').value;
            
            if (inicio && fin) {
                const minutos = parseTimeToMinutes(fin) - parseTimeToMinutes(inicio);
                if (minutos > 0) totalMinutos += minutos;
            }
        });

        return totalMinutos / 60;
    }

    // Actualizar contador de un cargo UCR específico
    function actualizarContadorCargoUCR(cargoBlock) {
        const jornadaSelect = cargoBlock.querySelector('.ucr-jornada-select');
        const jornada = jornadaSelect.options[jornadaSelect.selectedIndex];
        const horasObjetivo = jornada && jornada.value ? Number(jornada.dataset.horas) : 0;
        
        const horasAsignadasVal = calcularHorasCargo(cargoBlock);
        const horasRestantesVal = horasObjetivo - horasAsignadasVal;

        const targetHoras = cargoBlock.querySelector('.target-horas-ucr');
        const horasAsignadas = cargoBlock.querySelector('.horas-asignadas-ucr');
        const horasRestantes = cargoBlock.querySelector('.horas-restantes-ucr');

        targetHoras.textContent = horasObjetivo || '—';
        horasAsignadas.textContent = horasAsignadasVal.toFixed(1);
        horasRestantes.textContent = horasObjetivo ? horasRestantesVal.toFixed(1) : '—';
    }

    // Agregar nuevo cargo UCR
    addCargoUCR.addEventListener('click', () => {
        const nuevo = templateCargoUCR.cloneNode(true);
        
        // Obtener el nuevo índice de cargo
        const cargos = cargosUCRContainer.querySelectorAll('.cargo-ucr-block');
        const nuevoIndex = cargos.length;
        
        // Limpiar valores
        nuevo.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
        nuevo.querySelectorAll('input[type="date"]').forEach(i => i.value = '');
        nuevo.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        
        // Mantener solo una fila de horario inicial
        const horariosContainer = nuevo.querySelector('.horarios-cargo');
        horariosContainer.dataset.cargoIndex = nuevoIndex;
        const filas = horariosContainer.querySelectorAll('.fila-horario-ucr');
        for (let i = 1; i < filas.length; i++) {
            filas[i].remove();
        }
        
        // Actualizar el índice de cargo en la primera fila
        const primeraFila = horariosContainer.querySelector('.fila-horario-ucr');
        const indexField = primeraFila.querySelector('.cargo-index-field');
        if (indexField) {
            indexField.value = nuevoIndex;
        }
        
        cargosUCRContainer.appendChild(nuevo);
        actualizarContadorCargoUCR(nuevo);
    });

    // Event delegation para todos los cargos UCR
    cargosUCRContainer.addEventListener('click', (e) => {
        const cargoBlock = e.target.closest('.cargo-ucr-block');
        if (!cargoBlock) return;

        // Agregar horario a este cargo
        if (e.target.matches('.btn-add-horario-cargo')) {
            const horariosContainer = cargoBlock.querySelector('.horarios-cargo');
            const templateHorario = horariosContainer.querySelector('.fila-horario-ucr').cloneNode(true);
            
            // Obtener el índice de este cargo
            const cargoIndex = horariosContainer.dataset.cargoIndex || '0';
            
            // Limpiar valores
            templateHorario.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
            templateHorario.querySelectorAll('input[type="date"]').forEach(i => i.value = '');
            templateHorario.querySelector('select').selectedIndex = 0;
            
            // Actualizar el campo de índice de cargo
            const indexField = templateHorario.querySelector('.cargo-index-field');
            if (indexField) {
                indexField.value = cargoIndex;
            }
            
            horariosContainer.appendChild(templateHorario);
            actualizarContadorCargoUCR(cargoBlock);
        }

        // Eliminar horario de este cargo
        if (e.target.matches('.btn-remove-horario-ucr')) {
            const horariosContainer = cargoBlock.querySelector('.horarios-cargo');
            const filas = horariosContainer.querySelectorAll('.fila-horario-ucr');
            if (filas.length > 1) {
                e.target.closest('.fila-horario-ucr').remove();
                actualizarContadorCargoUCR(cargoBlock);
            } else {
                alert('Debe mantener al menos un horario por cargo');
            }
        }

        // Eliminar cargo completo
        if (e.target.matches('.btn-remove-cargo')) {
            const cargos = cargosUCRContainer.querySelectorAll('.cargo-ucr-block');
            if (cargos.length > 1) {
                cargoBlock.remove();
            } else {
                alert('Debe mantener al menos un cargo UCR');
            }
        }
    });

    // Actualizar contador cuando cambie la jornada o los horarios de un cargo
    cargosUCRContainer.addEventListener('change', (e) => {
        const cargoBlock = e.target.closest('.cargo-ucr-block');
        if (cargoBlock && (e.target.matches('.ucr-jornada-select') || e.target.matches('input[type="time"]'))) {
            actualizarContadorCargoUCR(cargoBlock);
        }
    });

    // Actualizar contador cuando se escribe en los inputs de tiempo
    cargosUCRContainer.addEventListener('input', (e) => {
        const cargoBlock = e.target.closest('.cargo-ucr-block');
        if (cargoBlock && e.target.matches('input[type="time"]')) {
            actualizarContadorCargoUCR(cargoBlock);
        }
    });

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

        // Validar cada cargo UCR individualmente
        const cargosUCR = cargosUCRContainer.querySelectorAll('.cargo-ucr-block');
        for (let i = 0; i < cargosUCR.length; i++) {
            const cargo = cargosUCR[i];
            const cargoSelect = cargo.querySelector('.ucr-cargo-select');
            const nombreCargo = cargoSelect.options[cargoSelect.selectedIndex]?.text || `Cargo ${i + 1}`;
            const jornadaSelect = cargo.querySelector('.ucr-jornada-select');
            const jornada = jornadaSelect.options[jornadaSelect.selectedIndex];
            const horasObjetivoCargo = jornada && jornada.value ? Number(jornada.dataset.horas) : 0;
            const horasAsignadasCargo = calcularHorasCargo(cargo);

            // Si hay jornada seleccionada, validar
            if (horasObjetivoCargo > 0) {
                if (!cargoSelect.value) {
                    e.preventDefault();
                    alert(`Cargo ${i + 1}: Debe seleccionar un cargo UCR`);
                    return;
                }

                if (horasAsignadasCargo === 0) {
                    e.preventDefault();
                    alert(`Cargo "${nombreCargo}":\nHa seleccionado una jornada pero no ha asignado horarios.`);
                    return;
                }

                if (horasAsignadasCargo !== horasObjetivoCargo) {
                    e.preventDefault();
                    const diferenciaCargo = horasAsignadasCargo - horasObjetivoCargo;
                    let mensajeCargo = `Cargo UCR "${nombreCargo}":\n`;
                    if (diferenciaCargo > 0) {
                        mensajeCargo += `❌ EXCEDE la jornada por ${Math.abs(diferenciaCargo).toFixed(1)} horas\n`;
                        mensajeCargo += `Asignadas: ${horasAsignadasCargo.toFixed(1)}h | Requeridas: ${horasObjetivoCargo}h`;
                    } else {
                        mensajeCargo += `❌ FALTAN ${Math.abs(diferenciaCargo).toFixed(1)} horas para completar la jornada\n`;
                        mensajeCargo += `Asignadas: ${horasAsignadasCargo.toFixed(1)}h | Requeridas: ${horasObjetivoCargo}h`;
                    }
                    alert(mensajeCargo);
                    return;
                }
            }
        }

        // Validar cada institución externa individualmente
        const instituciones = institucionesExternasContainer.querySelectorAll('.institucion-externa-block');
        for (let i = 0; i < instituciones.length; i++) {
            const institucion = instituciones[i];
            const nombreInstitucion = institucion.querySelector('input[name="ext_institucion[]"]').value;
            const jornadaSelect = institucion.querySelector('.ext-jornada-select');
            const jornada = jornadaSelect.options[jornadaSelect.selectedIndex];
            const horasObjetivoInst = jornada && jornada.value ? Number(jornada.dataset.horas) : 0;
            const horasAsignadasInst = calcularHorasInstitucion(institucion);

            // Si hay jornada seleccionada, validar
            if (horasObjetivoInst > 0) {
                if (!nombreInstitucion || nombreInstitucion.trim() === '') {
                    e.preventDefault();
                    alert(`Institución ${i + 1}: Debe especificar el nombre de la institución`);
                    return;
                }

                if (horasAsignadasInst === 0) {
                    e.preventDefault();
                    alert(`Institución "${nombreInstitucion}":\nHa seleccionado una jornada pero no ha asignado horarios.`);
                    return;
                }

                if (horasAsignadasInst !== horasObjetivoInst) {
                    e.preventDefault();
                    const diferenciaInst = horasAsignadasInst - horasObjetivoInst;
                    let mensajeInst = `Institución "${nombreInstitucion}":\n`;
                    if (diferenciaInst > 0) {
                        mensajeInst += `❌ EXCEDE la jornada por ${Math.abs(diferenciaInst).toFixed(1)} horas\n`;
                        mensajeInst += `Asignadas: ${horasAsignadasInst.toFixed(1)}h | Requeridas: ${horasObjetivoInst}h`;
                    } else {
                        mensajeInst += `❌ FALTAN ${Math.abs(diferenciaInst).toFixed(1)} horas para completar la jornada\n`;
                        mensajeInst += `Asignadas: ${horasAsignadasInst.toFixed(1)}h | Requeridas: ${horasObjetivoInst}h`;
                    }
                    alert(mensajeInst);
                    return;
                }
            }
        }
    });

    // ========== HORARIOS EXTERNOS - MÚLTIPLES INSTITUCIONES ==========
    const institucionesExternasContainer = document.getElementById('institucionesExternas');
    const addInstitucionExterna = document.getElementById('addInstitucionExterna');
    
    // Template para nueva institución
    const templateInstitucion = institucionesExternasContainer.querySelector('.institucion-externa-block').cloneNode(true);
    
    // Calcular horas asignadas para una institución específica
    function calcularHorasInstitucion(institucionBlock) {
        let totalMinutos = 0;
        const filas = institucionBlock.querySelectorAll('.fila-horario-externo');
        
        filas.forEach(fila => {
            const inicio = fila.querySelector('input[name="ext_hora_inicio[]"]').value;
            const fin = fila.querySelector('input[name="ext_hora_fin[]"]').value;
            
            if (inicio && fin) {
                const minutos = parseTimeToMinutes(fin) - parseTimeToMinutes(inicio);
                if (minutos > 0) totalMinutos += minutos;
            }
        });

        return totalMinutos / 60;
    }

    // Actualizar contador de una institución específica
    function actualizarContadorInstitucion(institucionBlock) {
        const jornadaSelect = institucionBlock.querySelector('.ext-jornada-select');
        const jornada = jornadaSelect.options[jornadaSelect.selectedIndex];
        const horasObjetivo = jornada && jornada.value ? Number(jornada.dataset.horas) : 0;
        
        const horasAsignadasVal = calcularHorasInstitucion(institucionBlock);
        const horasRestantesVal = horasObjetivo - horasAsignadasVal;

        const targetHoras = institucionBlock.querySelector('.target-horas');
        const horasAsignadas = institucionBlock.querySelector('.horas-asignadas');
        const horasRestantes = institucionBlock.querySelector('.horas-restantes');

        targetHoras.textContent = horasObjetivo || '—';
        horasAsignadas.textContent = horasAsignadasVal.toFixed(1);
        horasRestantes.textContent = horasObjetivo ? horasRestantesVal.toFixed(1) : '—';
    }

    // Agregar nueva institución externa
    addInstitucionExterna.addEventListener('click', () => {
        const nueva = templateInstitucion.cloneNode(true);
        
        // Obtener el nuevo índice de institución
        const instituciones = institucionesExternasContainer.querySelectorAll('.institucion-externa-block');
        const nuevoIndex = instituciones.length;
        
        // Limpiar valores
        nueva.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
        nueva.querySelectorAll('input[type="date"]').forEach(i => i.value = '');
        nueva.querySelectorAll('input[type="text"]').forEach(i => i.value = '');
        nueva.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        
        // Mantener solo una fila de horario inicial
        const horariosContainer = nueva.querySelector('.horarios-institucion');
        horariosContainer.dataset.instIndex = nuevoIndex;
        const filas = horariosContainer.querySelectorAll('.fila-horario-externo');
        for (let i = 1; i < filas.length; i++) {
            filas[i].remove();
        }
        
        // Actualizar el índice de institución en la primera fila
        const primeraFila = horariosContainer.querySelector('.fila-horario-externo');
        const indexField = primeraFila.querySelector('.inst-index-field');
        if (indexField) {
            indexField.value = nuevoIndex;
        }
        
        institucionesExternasContainer.appendChild(nueva);
        actualizarContadorInstitucion(nueva);
    });

    // Event delegation para todas las instituciones
    institucionesExternasContainer.addEventListener('click', (e) => {
        const institucionBlock = e.target.closest('.institucion-externa-block');
        if (!institucionBlock) return;

        // Agregar horario a esta institución
        if (e.target.matches('.btn-add-horario-institucion')) {
            const horariosContainer = institucionBlock.querySelector('.horarios-institucion');
            const templateHorario = horariosContainer.querySelector('.fila-horario-externo').cloneNode(true);
            
            // Obtener el índice de esta institución
            const instIndex = horariosContainer.dataset.instIndex || '0';
            
            // Limpiar valores
            templateHorario.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
            templateHorario.querySelectorAll('input[type="date"]').forEach(i => i.value = '');
            templateHorario.querySelector('select').selectedIndex = 0;
            
            // Actualizar el campo de índice de institución
            const indexField = templateHorario.querySelector('.inst-index-field');
            if (indexField) {
                indexField.value = instIndex;
            }
            
            horariosContainer.appendChild(templateHorario);
            actualizarContadorInstitucion(institucionBlock);
        }

        // Eliminar horario de esta institución
        if (e.target.matches('.btn-remove-horario-externo')) {
            const horariosContainer = institucionBlock.querySelector('.horarios-institucion');
            const filas = horariosContainer.querySelectorAll('.fila-horario-externo');
            if (filas.length > 1) {
                e.target.closest('.fila-horario-externo').remove();
                actualizarContadorInstitucion(institucionBlock);
            } else {
                alert('Debe mantener al menos un horario por institución');
            }
        }

        // Eliminar institución completa
        if (e.target.matches('.btn-remove-institucion')) {
            const instituciones = institucionesExternasContainer.querySelectorAll('.institucion-externa-block');
            if (instituciones.length > 1) {
                institucionBlock.remove();
            } else {
                alert('Debe mantener al menos una institución externa');
            }
        }
    });

    // Actualizar contador cuando cambie la jornada o los horarios de una institución
    institucionesExternasContainer.addEventListener('change', (e) => {
        const institucionBlock = e.target.closest('.institucion-externa-block');
        if (institucionBlock && (e.target.matches('.ext-jornada-select') || e.target.matches('input[type="time"]'))) {
            actualizarContadorInstitucion(institucionBlock);
        }
    });

    // Actualizar contador cuando se escribe en los inputs de tiempo
    institucionesExternasContainer.addEventListener('input', (e) => {
        const institucionBlock = e.target.closest('.institucion-externa-block');
        if (institucionBlock && e.target.matches('input[type="time"]')) {
            actualizarContadorInstitucion(institucionBlock);
        }
    });

    // Inicializar contadores
    // Inicializar contador de cada cargo UCR
    const cargosUCRIniciales = cargosUCRContainer.querySelectorAll('.cargo-ucr-block');
    cargosUCRIniciales.forEach(cargo => actualizarContadorCargoUCR(cargo));
    
    // Inicializar contador de cada institución externa
    const institucionesIniciales = institucionesExternasContainer.querySelectorAll('.institucion-externa-block');
    institucionesIniciales.forEach(inst => actualizarContadorInstitucion(inst));

    // ========== MANEJO DE SEDE Y UNIDAD ACADÉMICA ==========
    const sedeSelect = document.getElementById('id_sede');
    const unidadSelect = document.getElementById('id_unidad');

    sedeSelect.addEventListener('change', function() {
        const sedeId = this.value;
        
        console.log('Sede seleccionada:', sedeId); // Debug
        
        // Limpiar unidades
        unidadSelect.innerHTML = '<option value="">Cargando unidades...</option>';
        unidadSelect.disabled = true;
        
        if (sedeId) {
            // Hacer petición AJAX para obtener unidades de la sede
            const url = `/api/unidades-por-sede/${sedeId}`;
            console.log('Haciendo petición a:', url); // Debug
            
            fetch(url)
                .then(response => {
                    console.log('Respuesta recibida:', response.status); // Debug
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(unidades => {
                    console.log('Unidades recibidas:', unidades); // Debug
                    unidadSelect.innerHTML = '<option value="">Seleccione una unidad académica...</option>';
                    
                    if (unidades && unidades.length > 0) {
                        unidades.forEach(unidad => {
                            const option = document.createElement('option');
                            option.value = unidad.id_unidad;
                            option.textContent = unidad.nombre;
                            unidadSelect.appendChild(option);
                        });
                    } else {
                        unidadSelect.innerHTML = '<option value="">No hay unidades disponibles para esta sede</option>';
                    }
                    
                    unidadSelect.disabled = false;
                    unidadSelect.className = unidadSelect.className.replace('bg-gray-100', 'bg-gray-50 hover:bg-white');
                })
                .catch(error => {
                    console.error('Error al cargar unidades:', error);
                    unidadSelect.innerHTML = '<option value="">Error al cargar unidades</option>';
                });
        } else {
            unidadSelect.innerHTML = '<option value="">Primero seleccione una sede</option>';
            unidadSelect.disabled = true;
        }
    });
});
</script>
@endsection
