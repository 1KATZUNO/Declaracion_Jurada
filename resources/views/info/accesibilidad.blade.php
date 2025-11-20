@extends('layout')

@section('titulo', 'Accesibilidad')

@section('contenido')
<div class="container mx-auto w-full max-w-6xl px-4 md:px-8 py-8">
    <div class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden transition-colors duration-300">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <h1 class="text-3xl font-bold text-white">♿ Accesibilidad</h1>
            <p class="text-purple-50 text-sm mt-2">Comprometidos con la inclusión digital</p>
        </div>

        <div class="p-8 space-y-8">
            <!-- Introducción -->
            <div class="bg-purple-50 border-l-4 border-purple-600 p-6 rounded">
                <h2 class="text-xl font-semibold text-purple-900 mb-3">Nuestro Compromiso</h2>
                <p class="text-gray-700 leading-relaxed">
                    En la Universidad de Costa Rica nos comprometemos a garantizar que nuestro sistema de Declaraciones Juradas 
                    sea accesible para todas las personas, independientemente de sus capacidades o del dispositivo que utilicen.
                </p>
            </div>

            <!-- Características de Accesibilidad -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="text-purple-600">✓</span>
                    Características de Accesibilidad Implementadas
                </h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Navegación por teclado -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <span class="text-3xl">⌨️</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Navegación por Teclado</h3>
                                <p class="text-sm text-gray-600">
                                    Todos los elementos interactivos son accesibles mediante el teclado usando Tab, Enter y teclas de dirección.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contraste de colores -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <span class="text-3xl">🎨</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Alto Contraste</h3>
                                <p class="text-sm text-gray-600">
                                    Utilizamos combinaciones de colores con contraste suficiente para facilitar la lectura.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Texto alternativo -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <span class="text-3xl">🖼️</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Texto Alternativo</h3>
                                <p class="text-sm text-gray-600">
                                    Todas las imágenes incluyen descripciones alternativas para lectores de pantalla.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Diseño responsive -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <span class="text-3xl">📱</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Diseño Adaptable</h3>
                                <p class="text-sm text-gray-600">
                                    El sistema se adapta a diferentes tamaños de pantalla y dispositivos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes claros -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <span class="text-3xl">💬</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Mensajes Descriptivos</h3>
                                <p class="text-sm text-gray-600">
                                    Los errores y notificaciones incluyen mensajes claros y accionables.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Formularios etiquetados -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3">
                            <span class="text-3xl">📋</span>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Formularios Accesibles</h3>
                                <p class="text-sm text-gray-600">
                                    Todos los campos de formulario tienen etiquetas descriptivas asociadas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atajos de teclado -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-purple-600">⌨️</span>
                    Atajos de Teclado Útiles
                </h2>
                <div class="bg-gray-50 rounded-lg p-6 space-y-3">
                    <div class="flex items-center gap-4">
                        <kbd class="px-3 py-1 bg-white border-2 border-gray-300 rounded text-sm font-mono shadow-sm">Tab</kbd>
                        <span class="text-gray-700">Navegar al siguiente elemento</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <kbd class="px-3 py-1 bg-white border-2 border-gray-300 rounded text-sm font-mono shadow-sm">Shift + Tab</kbd>
                        <span class="text-gray-700">Navegar al elemento anterior</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <kbd class="px-3 py-1 bg-white border-2 border-gray-300 rounded text-sm font-mono shadow-sm">Enter</kbd>
                        <span class="text-gray-700">Activar botón o enlace seleccionado</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <kbd class="px-3 py-1 bg-white border-2 border-gray-300 rounded text-sm font-mono shadow-sm">Esc</kbd>
                        <span class="text-gray-700">Cerrar modal o menú desplegable</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <kbd class="px-3 py-1 bg-white border-2 border-gray-300 rounded text-sm font-mono shadow-sm">↑ ↓</kbd>
                        <span class="text-gray-700">Navegar en menús desplegables</span>
                    </div>
                </div>
            </div>

            <!-- Tecnologías compatibles -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-purple-600">🔧</span>
                    Tecnologías de Asistencia Compatibles
                </h2>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start gap-3">
                        <span class="text-purple-600 font-bold">•</span>
                        <span><strong>Lectores de pantalla:</strong> JAWS, NVDA, VoiceOver, TalkBack</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-purple-600 font-bold">•</span>
                        <span><strong>Ampliadores de pantalla:</strong> ZoomText, Windows Magnifier</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-purple-600 font-bold">•</span>
                        <span><strong>Navegación por voz:</strong> Dragon NaturallySpeaking</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-purple-600 font-bold">•</span>
                        <span><strong>Navegadores modernos:</strong> Chrome, Firefox, Safari, Edge</span>
                    </li>
                </ul>
            </div>

            <!-- Contacto -->
            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded">
                <h2 class="text-xl font-semibold text-blue-900 mb-3">¿Encontró alguna barrera de accesibilidad?</h2>
                <p class="text-gray-700 mb-4">
                    Si encuentra algún problema de accesibilidad en nuestro sistema, por favor contáctenos. 
                    Su retroalimentación nos ayuda a mejorar continuamente.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="mailto:soporte@ucr.ac.cr" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span>📧</span>
                        <span>soporte@ucr.ac.cr</span>
                    </a>
                    <a href="{{ route('ayuda') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                        <span>❓</span>
                        <span>Ir a Ayuda</span>
                    </a>
                </div>
            </div>

            <!-- Estándares -->
            <div class="text-sm text-gray-600 border-t pt-6">
                <p>
                    <strong>Estándares seguidos:</strong> Este sistema se desarrolla siguiendo las pautas de 
                    <a href="https://www.w3.org/WAI/WCAG21/quickref/" target="_blank" class="text-purple-600 hover:underline">
                        WCAG 2.1 nivel AA
                    </a>
                    y las mejores prácticas de accesibilidad web.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
