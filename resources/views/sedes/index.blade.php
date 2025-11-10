@extends('layout')
 @csrf
@section('titulo', 'Sedes')

@section('contenido')
<div class="container mx-auto w-full max-w-7xl px-2 sm:px-4 md:px-8 py-8">
    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-md bg-red-50 text-red-700 text-sm border border-red-200">
        {{ session('error') }}
    </div>
@endif

@if(session('ok'))
    <div class="mb-4 px-4 py-3 rounded-md bg-green-50 text-green-700 text-sm border border-green-200">
        {{ session('ok') }}
    </div>
@endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">

        <div class="px-8 py-6 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-900 tracking-tight">SEDES</h2>
            <p class="text-gray-500 text-sm mt-1">Gestione y consulte las sedes universitarias.</p>
        </div>

        <div class="p-8 space-y-8">

            <section>
                <h3 class="text-xs font-semibold text-gray-600 tracking-wider uppercase">Criterio de búsqueda</h3>
                <div class="mt-4">
                    <form method="GET" action="{{ route('sedes.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la sede</label>
                            <input type="text" name="nombre" value="{{ request('nombre') }}"
                                    placeholder="Ej: Sede Guanacaste"
                                    class="w-full rounded-md border-gray-300 focus:border-blue-600 focus:ring-blue-600">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                            <input type="text" name="ubicacion" value="{{ request('ubicacion') }}"
                                    placeholder="Ej: Liberia"
                                    class="w-full rounded-md border-gray-300 focus:border-blue-600 focus:ring-blue-600">
                        </div>

                        <div class="col-span-1 flex items-end gap-3">
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-md bg-blue-700 text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600">
                                Buscar
                            </button>
                            <a href="{{ route('sedes.index') }}"
                                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </section>

            <section>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <a href="{{ route('sedes.create') }}"
                        class="flex items-center gap-3 rounded-md border border-gray-200 px-4 py-3 hover:bg-gray-50">
                        {{-- icono plus --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-800">Agregar Sede</span>
                    </a>
            </section>

            {{-- LISTA DE SEDES --}}
            <section>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-xs font-semibold text-gray-600 tracking-wider uppercase">Se – Lista de sedes</h3>
                    
                </div>

                <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Nombre de la Sede
                                </th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Ubicación
                                </th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
    @forelse ($sedes as $s)
        <tr class="hover:bg-gray-50 transition-colors">
            {{-- Nombre --}}
            <td class="py-4 px-4 text-sm text-gray-900 font-medium">
                {{ $s->nombre }}
            </td>

            {{-- Ubicación --}}
            <td class="py-4 px-4 text-sm text-gray-600">
                {{ $s->ubicacion ?? '—' }}
            </td>

            {{-- Acciones --}}
            <td class="py-4 px-4 text-sm">
                <div class="flex items-center gap-2">

                    {{-- Editar --}}
                    <a href="{{ route('sedes.edit', $s->id_sede) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-yellow-300 bg-yellow-50 text-yellow-800 hover:bg-yellow-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 012.828 2.828l-9.193 9.193a2 2 0 01-.878.497l-3.357.839a.5.5 0 01-.606-.606l.84-3.357a2 2 0 01.497-.878l9.193-9.193z"/>
                        </svg>
                        Editar
                    </a>

                    @if($s->unidades_academicas_count == 0)
                        {{-- Eliminar habilitado --}}
                        <form action="{{ route('sedes.destroy', $s->id_sede) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar esta sede?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-red-300 bg-red-50 text-red-700 hover:bg-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 7a1 1 0 011-1h6a1 1 0 011 1v9a2 2 0 01-2 2H8a2 2 0 01-2-2V7zm3-4a1 1 0 00-1 1v1H6a1 1 0 000 2h8a1 1 0 100-2h-2V4a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Eliminar
                            </button>
                        </form>
                    @else
                        {{-- No eliminable --}}
                        <button type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded border border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed"
                                title="No se puede eliminar: tiene unidades académicas asociadas.">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 4a.75.75 0 00-1.5 0v5a.75.75 0 001.5 0V6zm-1.5 7.5a.75.75 0 011.5 0v1a.75.75 0 01-1.5 0v-1z" clip-rule="evenodd"/>
                            </svg>
                            UA Asociada
                        </button>
                    @endif

                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="py-12 text-center text-sm text-gray-500">
                No hay sedes que coincidan con los criterios.
            </td>
        </tr>
    @endforelse
</tbody>

                    </table>
                </div>

                {{-- Paginación (si usas ->paginate()) --}}
                @if(method_exists($sedes, 'links'))
                    <div class="mt-4">
                        {{ $sedes->appends(request()->query())->links() }}
                    </div>
                @endif
            </section>

        </div>
    </div>
</div>
@endsection
