<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Declaraciones UCR</title>
    @vite(['resources/css/app.css'])
</head>
<body 
    class="min-h-screen bg-cover bg-center bg-no-repeat flex items-start justify-center pt-30
"
    style="background-image: url('{{ asset('imagenes/Login.png') }}');">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-center">
                    <h2 class="text-3xl font-bold text-white">
                        Declaraciones UCR
                    </h2>
                    <p class="mt-2 text-blue-100">
                        Iniciar Sesión
                    </p>
                    
                </div>

                <!-- Form -->
                <div class="px-6 py-8">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Correo o Identificación
                            </label>
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                value="{{ old('username') }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                                placeholder="correo@ejemplo.com"
                            >
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                                placeholder="••••••••"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button 
                                type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                            >
                                Iniciar Sesión
                            </button>
                        </div>
                        <!-- Enlace a cambiar contraseña -->
<div class="mt-4 text-center">
    <a href="{{ route('password.form') }}" class="text-blue-600 hover:underline text-sm">
        ¿Olvidaste tu contraseña o deseas cambiarla?
    </a>
</div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Sistema de Declaraciones Juradas
                </p>
            </div>
        </div>
    </div>

    {{-- 
    <h2 class="text-2xl font-bold text-gray-800">Declaraciones juradas</h2>
    @if(session('usuario_rol') === 'admin')
        <p class="text-sm text-gray-600">Bienvenido administrador. Tienes acceso completo.</p>
    @elseif(session('usuario_rol') === 'funcionario')
        <p class="text-sm text-gray-600">Bienvenido funcionario. Puedes gestionar tus declaraciones.</p>
    @endif
    --}}
</body>
</html>
