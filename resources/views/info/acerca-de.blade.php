@extends('layout')

@section('titulo', 'Acerca de')

@section('contenido')
<div class="container mx-auto w-full max-w-6xl px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-800 to-blue-900 px-8 py-12 text-center">
            <div class="mb-4">
                <img src="{{ asset('imagenes/uc_logo.png') }}" 
                     alt="UCR Logo" 
                     class="h-20 mx-auto mb-4 filter brightness-0 invert">
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Sistema de Declaraciones Juradas</h1>
            <p class="text-blue-200 text-lg">Universidad de Costa Rica</p>
            <div class="mt-4 inline-block px-4 py-2 bg-white/20 rounded-full">
                <span class="text-white font-semibold">Versión 1.0.0</span>
            </div>
        </div>

        <div class="p-8 space-y-8">
            <!-- Descripción del sistema -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">📋 Acerca del Sistema</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    El Sistema de Declaraciones Juradas es una plataforma web desarrollada para la Universidad de Costa Rica 
                    con el objetivo de facilitar la gestión, registro y seguimiento de las declaraciones juradas del personal 
                    académico y administrativo de la institución.
                </p>
                <p class="text-gray-700 leading-relaxed">
                    Este sistema permite a los funcionarios declarar sus actividades laborales tanto dentro como fuera de la 
                    UCR, garantizando transparencia y cumplimiento de las normativas institucionales.
                </p>
            </div>

            <!-- Características principales -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">✨ Características Principales</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <span class="text-2xl">📝</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Gestión de Declaraciones</h3>
                            <p class="text-sm text-gray-600">Crear, editar y visualizar declaraciones juradas</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-green-50 rounded-lg border border-green-200">
                        <span class="text-2xl">⏰</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Control de Horarios</h3>
                            <p class="text-sm text-gray-600">Registro detallado de horarios laborales</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <span class="text-2xl">📄</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Exportación</h3>
                            <p class="text-sm text-gray-600">Generación de reportes en Excel y PDF</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <span class="text-2xl">🔔</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                            <p class="text-sm text-gray-600">Alertas y recordatorios automáticos</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
                        <span class="text-2xl">🔐</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Seguridad</h3>
                            <p class="text-sm text-gray-600">Autenticación segura y control de acceso</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                        <span class="text-2xl">📱</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Multi-dispositivo</h3>
                            <p class="text-sm text-gray-600">Acceso desde cualquier dispositivo</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tecnologías -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">🛠️ Tecnologías Utilizadas</h2>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">Backend</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center gap-2">
                                    <span class="text-red-600">●</span>
                                    <span>Laravel 11</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-blue-600">●</span>
                                    <span>PHP 8.2+</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-orange-600">●</span>
                                    <span>MySQL 8.0</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">Frontend</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center gap-2">
                                    <span class="text-cyan-600">●</span>
                                    <span>Tailwind CSS</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-yellow-600">●</span>
                                    <span>JavaScript ES6+</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-green-600">●</span>
                                    <span>Blade Templates</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">Librerías</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center gap-2">
                                    <span class="text-green-600">●</span>
                                    <span>Maatwebsite/Excel</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-red-600">●</span>
                                    <span>DomPDF</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-purple-600">●</span>
                                    <span>Laravel Notifications</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipo de desarrollo -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">👥 Desarrollo</h2>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                    <p class="text-gray-700 mb-4">
                        Este sistema ha sido desarrollado por la Universidad de Costa Rica como parte de la 
                        iniciativa de digitalización y mejora de procesos administrativos.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <div class="px-4 py-2 bg-white rounded-full text-sm font-medium text-gray-700 shadow-sm">
                            Análisis de Requisitos
                        </div>
                        <div class="px-4 py-2 bg-white rounded-full text-sm font-medium text-gray-700 shadow-sm">
                            Desarrollo Full Stack
                        </div>
                        <div class="px-4 py-2 bg-white rounded-full text-sm font-medium text-gray-700 shadow-sm">
                            Testing & QA
                        </div>
                        <div class="px-4 py-2 bg-white rounded-full text-sm font-medium text-gray-700 shadow-sm">
                            Diseño UX/UI
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de contacto -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">📞 Contacto y Soporte</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="text-xl">📧</span>
                            Correo Electrónico
                        </h3>
                        <a href="mailto:soporte@ucr.ac.cr" 
                           class="text-blue-600 hover:underline font-medium">
                            soporte@ucr.ac.cr
                        </a>
                        <p class="text-sm text-gray-600 mt-2">
                            Para consultas, reportes de errores o sugerencias
                        </p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="text-xl">🌐</span>
                            Sitio Web UCR
                        </h3>
                        <a href="https://www.ucr.ac.cr" 
                           target="_blank"
                           class="text-blue-600 hover:underline font-medium">
                            www.ucr.ac.cr
                        </a>
                        <p class="text-sm text-gray-600 mt-2">
                            Portal oficial de la Universidad de Costa Rica
                        </p>
                    </div>
                </div>
            </div>

            <!-- Política y términos -->
            <div class="border-t pt-6">
                <div class="flex flex-wrap gap-6 text-sm text-gray-600">
                    <a href="#" class="hover:text-blue-600 hover:underline">Política de Privacidad</a>
                    <a href="#" class="hover:text-blue-600 hover:underline">Términos de Uso</a>
                    <a href="#" class="hover:text-blue-600 hover:underline">Seguridad de Datos</a>
                    <a href="{{ route('accesibilidad') }}" class="hover:text-blue-600 hover:underline">Accesibilidad</a>
                </div>
            </div>

            <!-- Footer informativo -->
            <div class="bg-blue-900 text-white rounded-lg p-6 text-center">
                <p class="text-sm mb-2">
                    © {{ date('Y') }} Universidad de Costa Rica. Todos los derechos reservados.
                </p>
                <p class="text-xs text-blue-300">
                    Sistema de Declaraciones Juradas - Versión 1.0.0 | Última actualización: {{ date('F Y') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
