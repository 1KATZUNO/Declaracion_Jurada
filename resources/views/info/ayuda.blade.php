@extends('layout')

@section('titulo', 'Ayuda - Manual de Usuario')

@section('contenido')
<div class="container mx-auto w-full max-w-7xl px-4 md:px-8 py-8">
    <div class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden transition-colors duration-300">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
            <h1 class="text-3xl font-bold text-white">❓ Centro de Ayuda</h1>
            <p class="text-green-50 text-sm mt-2">Manual de usuario y preguntas frecuentes</p>
        </div>

        <div class="p-8">
            <!-- Barra de búsqueda -->
            <div class="mb-8">
                <form action="{{ route('ayuda') }}" method="GET" class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $searchTerm }}"
                           placeholder="🔍 Buscar en el manual de usuario..."
                           class="w-full px-6 py-4 text-lg border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all shadow-sm"
                           autofocus>
                    <button type="submit" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium">
                        Buscar
                    </button>
                </form>
                @if(!empty($searchTerm))
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                        <span>Mostrando resultados para:</span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-medium">{{ $searchTerm }}</span>
                        <a href="{{ route('ayuda') }}" class="text-green-600 hover:underline ml-2">Limpiar búsqueda</a>
                    </div>
                @endif
            </div>

            @if(count($manual) === 0)
                <!-- Sin resultados -->
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No se encontraron resultados</h3>
                    <p class="text-gray-600 mb-4">Intenta con otros términos de búsqueda</p>
                    <a href="{{ route('ayuda') }}" 
                       class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Ver todo el manual
                    </a>
                </div>
            @else
                <!-- Secciones del manual -->
                <div class="space-y-6">
                    @foreach($manual as $index => $seccion)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
                                <div class="flex items-start gap-4">
                                    <span class="text-4xl">{{ $seccion['icono'] }}</span>
                                    <div class="flex-1">
                                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $seccion['titulo'] }}</h2>
                                        <p class="text-gray-700 leading-relaxed">{{ $seccion['contenido'] }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-white">
                                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                    <span class="text-green-600">📋</span>
                                    Pasos a seguir:
                                </h3>
                                <ol class="space-y-2">
                                    @foreach($seccion['pasos'] as $paso)
                                        <li class="flex gap-3 items-start">
                                            <span class="flex-shrink-0 w-6 h-6 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                            <span class="text-gray-700 pt-0.5">{{ $paso }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Sección adicional de ayuda -->
            <div class="mt-12 grid md:grid-cols-3 gap-6">
                <!-- Videos tutoriales -->
                <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-lg">
                    <div class="text-3xl mb-3">🎥</div>
                    <h3 class="font-semibold text-blue-900 mb-2">Videos Tutoriales</h3>
                    <p class="text-sm text-gray-700 mb-3">
                        Próximamente dispondremos de videos paso a paso para facilitar el uso del sistema.
                    </p>
                </div>

                <!-- Preguntas frecuentes -->
                <div class="bg-purple-50 border-l-4 border-purple-600 p-6 rounded-lg">
                    <div class="text-3xl mb-3">💡</div>
                    <h3 class="font-semibold text-purple-900 mb-2">Preguntas Frecuentes</h3>
                    <p class="text-sm text-gray-700 mb-3">
                        Consulta las dudas más comunes sobre el uso del sistema de declaraciones.
                    </p>
                </div>

                <!-- Soporte técnico -->
                <div class="bg-orange-50 border-l-4 border-orange-600 p-6 rounded-lg">
                    <div class="text-3xl mb-3">🛠️</div>
                    <h3 class="font-semibold text-orange-900 mb-2">Soporte Técnico</h3>
                    <p class="text-sm text-gray-700 mb-3">
                        ¿Necesitas ayuda adicional? Contáctanos en:
                    </p>
                    <a href="mailto:soporte@ucr.ac.cr" class="text-orange-600 hover:underline text-sm font-medium">
                        soporte@ucr.ac.cr
                    </a>
                </div>
            </div>

            <!-- Consejos rápidos -->
            <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="font-semibold text-green-900 mb-4 flex items-center gap-2">
                    <span class="text-2xl">💡</span>
                    Consejos Rápidos
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold">•</span>
                        <span>Revisa tu correo electrónico regularmente para recibir notificaciones del sistema</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold">•</span>
                        <span>Mantén actualizada tu información de perfil para una mejor experiencia</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold">•</span>
                        <span>Exporta tus declaraciones periódicamente como respaldo</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold">•</span>
                        <span>Si encuentras un error, repórtalo al equipo de soporte con capturas de pantalla</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-600 font-bold">•</span>
                        <span>Cierra sesión al terminar, especialmente en computadoras compartidas</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
