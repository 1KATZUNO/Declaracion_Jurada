<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarRol
{
    public function handle(Request $request, Closure $next, $rolPermitido)
    {
        if (session('usuario_rol') !== $rolPermitido) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
