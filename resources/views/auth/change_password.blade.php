<!DOCTYPE html>
 @csrf
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Declaraciones UCR</title>
    @vite(['resources/css/app.css'])
</head>
<body 
    class="min-h-screen bg-cover bg-center bg-no-repeat flex items-center justify-center"
    style="background-image: url('{{ asset('imagenes/Login.png') }}');"
>
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            
            <!-- Header -->
            <div class="bg-blue-600 px-6 py-8 text-center">
                <h2 class="text-3xl font-bold text-white">
                    Declaraciones UCR
                </h2>
                <p class="mt-2 text-blue-100">
                    Cambiar Contraseña
                </p>
            </div>

            <!-- Form -->
            <div class="px-6 py-8">
                @include('components.flash')

                <form method="POST" action="{{ route('password.change') }}" class="space-y-6">
                    @csrf

                    <!-- Correo -->
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-2">Correo</label>
                        <input type="email" name="correo" id="correo" value="{{ old('correo') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('correo') border-red-500 @enderror"
                            placeholder="correo@ejemplo.com"
                        >
                        @error('correo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña actual -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña actual</label>
                        <input type="password" name="current_password" id="current_password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                            placeholder="••••••••"
                        >
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nueva contraseña -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Nueva contraseña</label>
                        <input type="password" name="new_password" id="new_password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('new_password') border-red-500 @enderror"
                            placeholder="••••••••"
                        >
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar nueva contraseña -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar nueva contraseña</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••••"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                        >
                            Cambiar Contraseña
                        </button>
                    </div>

                    <!-- Volver al login -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline text-sm">
                            Volver al inicio de sesión
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Sistema de Declaraciones Juradas
                </p>
            </div>

        </div>
    </div>
</body>
</html>