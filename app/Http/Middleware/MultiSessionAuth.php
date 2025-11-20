<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MultiSessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Obtener el token de sesión de la cookie o header
        $sessionToken = $request->cookie('app_session_token') 
                     ?? $request->header('X-Session-Token')
                     ?? session('current_session_token');

        if ($sessionToken) {
            // Buscar los datos de sesión para este token
            $sessionData = session('auth_sessions.' . $sessionToken);
            
            if ($sessionData) {
                // Restaurar datos de sesión para esta request
                session([
                    'usuario_id' => $sessionData['usuario_id'],
                    'usuario_nombre' => $sessionData['usuario_nombre'],
                    'usuario_rol' => $sessionData['usuario_rol'],
                    'usuario_avatar' => $sessionData['usuario_avatar'],
                    'current_session_token' => $sessionToken,
                ]);
            }
        }

        // Verificar autenticación
        if (!session('usuario_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autenticado'], 401);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
