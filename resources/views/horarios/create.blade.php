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

            {{-- Mensaje de errores (opcional) --}}
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <p class="font-semibold mb-1">Por favor corrige los siguientes campos:</p>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Declaración: hidden si viene por URL; si no, select con espacio inferior --}}
            @if(request('id_declaracion'))
                <input type="hidden" name="id_declaracion" value="{{ request('id_declaracion') }}">
            @else
                <div class="mb-8"> {{-- 👈 espacio entre Declaración y Tipo --}}
                    <label for="id_declaracion" class="block text-sm font-medium text-gray-700 mb-2">
                        Declaración
                    </label>
                    <select id="id_declaracion" name="id_declaracion"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="">Seleccione…</option>
                        @foreach($declaraciones as $d)
                            <option value="{{ $d->id_declaracion }}"
                                @selected(old('id_declaracion') == $d->id_declaracion)>
                                Declaración #{{ $d->id_declaracion }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_declaracion')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="space-y-6">
                <!-- Tipo de institución -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de institución</label>
                    <select id="tipo" name="tipo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="ucr" @selected(old('tipo','ucr')==='ucr')>UCR</option>
                        <option value="externo" @selected(old('tipo')==='externo')>Otra institución pública o privada</option>
                    </select>
                    @error('tipo')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lugar / Institución (visible solo si tipo = externo) -->
                <div id="lugar-group" class="{{ old('tipo')==='externo' ? '' : 'hidden' }}">
                    <label for="lugar" class="block text-sm font-medium text-gray-700 mb-2">Lugar / Institución</label>
                    <input id="lugar" name="lugar" type="text" maxlength="255"
                           value="{{ old('lugar') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nombre de la otra institución (opcional)">
                    @error('lugar')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Día -->
                <div>
                    <label for="dia" class="block text-sm font-medium text-gray-700 mb-2">Día</label>
                    <select id="dia" name="dia"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @foreach (['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $d)
                            <option value="{{ $d }}" @selected(old('dia')===$d)>{{ $d }}</option>
                        @endforeach
                    </select>
                    @error('dia')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Horario -->
                <div class="bg-white p-5 rounded-2xl shadow-md space-y-4 w-full max-w-md">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Horario</h2>

                    <div class="flex items-center justify-between space-x-4">
                        <!-- Hora de inicio -->
                        <div class="flex flex-col w-1/2">
                            <label for="hora_inicio" class="text-sm font-medium text-gray-600 mb-1">Hora de inicio</label>
                            <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500">
                                <input type="time" id="hora_inicio" name="hora_inicio"
                                       value="{{ old('hora_inicio') }}"
                                       class="bg-transparent flex-1 text-gray-800 outline-none" required>
                            </div>
                            @error('hora_inicio')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hora de fin -->
                        <div class="flex flex-col w-1/2">
                            <label for="hora_fin" class="text-sm font-medium text-gray-600 mb-1">Hora de fin</label>
                            <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500">
                                <input type="time" id="hora_fin" name="hora_fin"
                                       value="{{ old('hora_fin') }}"
                                       class="bg-transparent flex-1 text-gray-800 outline-none" required>
                            </div>
                            @error('hora_fin')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
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
            </div>
        </form>
    </div>
</div>

<script>
  const tipoSelect = document.getElementById('tipo');
  const lugarGroup = document.getElementById('lugar-group');

  function toggleLugar() {
    if (tipoSelect.value === 'externo') {
      lugarGroup.classList.remove('hidden');
    } else {
      lugarGroup.classList.add('hidden');
      const inp = lugarGroup.querySelector('input');
      if (inp) inp.value = '';
    }
  }

  // Inicializa según el valor actual (por si vuelve con errores)
  toggleLugar();
  // Y escucha cambios
  tipoSelect.addEventListener('change', toggleLugar);
</script>
@endsection
