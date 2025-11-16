<?php

namespace App\Http\Middleware;

use App\Models\ActividadLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogActivityRequests
{
    /** @var array<string> */
    protected array $excluded = [
        'notificaciones-unread', 'storage/*', 'imagenes/*', 'broadcasting/*',
        'sanctum/*', '_ignition/*', 'telescope/*', 'debugbar*',
    ];

    /** @var array<string> */
    protected array $sensitive = [
        '_token', '_method', 'password', 'contrasena', 'password_confirmation',
        'remember', 'token', 'current_password', 'new_password', 'avatar',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET')) {
            return $response;
        }
        foreach ($this->excluded as $pattern) {
            if ($request->is($pattern)) {
                return $response;
            }
        }

        try {
            $route = $request->route();
            $routeName = $route?->getName() ?? '';
            $uri = $request->path();

            $accion = match (true) {
                $uri === 'login' || $routeName === 'login' => 'login',
                $uri === 'logout' || $routeName === 'logout' => 'logout',
                $request->isMethod('POST') => 'crear',
                $request->isMethod('PUT'), $request->isMethod('PATCH') => 'editar',
                $request->isMethod('DELETE') => 'eliminar',
                default => strtoupper($request->method()),
            };

            $modulo = 'General';
            if ($routeName && str_contains($routeName, '.')) {
                $modulo = Str::of($routeName)->before('.')->replace('-', ' ')->title();
            } else {
                $modulo = Str::of($uri)->before('/')->replace('-', ' ')->title();
                if ($modulo == '') $modulo = 'General';
            }

            $params = $route?->parameters() ?? [];
            $idRegistro = null;
            foreach ($params as $key => $val) {
                if (is_scalar($val) && preg_match('/(^id$|^id_|_id$|^id[A-Z]|[a-z]id$)/i', (string)$key)) {
                    $idRegistro = (int) $val; break;
                }
                if (is_object($val) && method_exists($val, 'getKey')) {
                    $idRegistro = (int) $val->getKey(); break;
                }
            }

            $input = collect($request->all())
                ->except($this->sensitive)
                ->map(function ($v) {
                    if (is_string($v) && Str::length($v) > 2000) {
                        return Str::substr($v, 0, 2000) . '…';
                    }
                    return $v;
                })->toArray();

            $descripcion = sprintf('%s %s', strtoupper($request->method()), '/' . $uri);

            ActividadLog::registrar(
                $accion,
                (string)$modulo,
                $descripcion,
                $idRegistro,
                null,
                $input ?: null,
            );
        } catch (\Throwable $e) {
            // Silencioso: no romper la petición si falla el logging
        }

        return $response;
    }
}
