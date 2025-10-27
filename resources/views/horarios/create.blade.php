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

                <!-- Lugar / Institución (visible solo si tipo = externo) -->
                <div id="lugar-group" class="hidden">
                    <label for="lugar" class="block text-sm font-medium text-gray-700 mb-2">Lugar / Institución</label>
                    <input id="lugar" name="lugar" type="text" maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre de la otra institución (opcional)">
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

               <div class="bg-white p-5 rounded-2xl shadow-md space-y-4 w-full max-w-md">
  <h2 class="text-lg font-semibold text-gray-800 mb-3">Horario</h2>

  <div class="flex items-center justify-between space-x-4">
    <!-- Hora de inicio -->
    <div class="flex flex-col w-1/2">
      <label for="hora_inicio" class="text-sm font-medium text-gray-600 mb-1">Hora de inicio</label>
      <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v3.586l2.293 2.293a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd" />
        </svg>
        <input type="time" id="hora_inicio" name="hora_inicio"
               class="bg-transparent flex-1 text-gray-800 outline-none" required>
      </div>
    </div>

    <!-- Hora de fin -->
    <div class="flex flex-col w-1/2">
      <label for="hora_fin" class="text-sm font-medium text-gray-600 mb-1">Hora de fin</label>
      <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v3.586l2.293 2.293a1 1 0 101.414-1.414L11 9.586V6z" clip-rule="evenodd" />
        </svg>
        <input type="time" id="hora_fin" name="hora_fin"
               class="bg-transparent flex-1 text-gray-800 outline-none" required>
      </div>
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
        </form>
    </div>
</div>

<script>
document.getElementById('tipo').addEventListener('change', function(){
    const grupo = document.getElementById('lugar-group');
    if (this.value === 'externo') {
        grupo.classList.remove('hidden');
    } else {
        grupo.classList.add('hidden');
        // limpiar valor cuando no sea externo
        const inp = grupo.querySelector('input');
        if (inp) inp.value = '';
    }
});
</script>
@endsection
