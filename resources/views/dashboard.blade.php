@extends('layout')

@section('titulo', 'Panel Principal')

@section('contenido')
 @csrf
<div class="container mx-auto w-full max-w-6xl px-2 sm:px-4 md:px-8 py-8">
    <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-blue-600 border-b border-gray-200">
                        <h2 class="text-2xl font-bold text-white">Declaraciones UCR</h2>
                    </div>
                    <div class="p-8">
                        <p class="text-gray-600 text-center mb-8">
                            Gestione usuarios, declaraciones, cargos, unidades académicas y más desde un solo lugar.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <a href="{{ route('usuarios.index') }}"
                               class="block p-6 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Usuarios</h3>
                                <p class="text-sm text-gray-600">Gestión de usuarios del sistema</p>
                            </a>
                            <a href="{{ route('declaraciones.index') }}"
                               class="block p-6 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Declaraciones</h3>
                                <p class="text-sm text-gray-600">Declaraciones juradas registradas</p>
                            </a>
                            <a href="{{ route('documentos.index') }}"
                               class="block p-6 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Documentos</h3>
                                <p class="text-sm text-gray-600">Archivos generados</p>
                            </a>
                            <a href="{{ route('notificaciones.index') }}"
                               class="block p-6 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Notificaciones</h3>
                                <p class="text-sm text-gray-600">Gestión de notificaciones</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
