<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadLog extends Model
{
    protected $table = 'actividad_logs';
    protected $primaryKey = 'id_actividad';
    
    protected $fillable = [
        'id_usuario',
        'correo_usuario',
        'accion',
        'modulo',
        'descripcion',
        'id_registro',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Método estático para registrar actividad
    public static function registrar($accion, $modulo, $descripcion, $idRegistro = null, $datosAnteriores = null, $datosNuevos = null)
    {
        $user = auth()->user();
        $userId = null;
        $userCorreo = null;

        if ($user) {
            // Soporta modelo Usuario personalizado o User estándar
            $userId = $user->id_usuario ?? $user->getAuthIdentifier() ?? $user->id ?? null;
            $userCorreo = $user->correo ?? $user->email ?? null;
        } else {
            // Fallback a sesión propia del sistema
            try {
                $sid = session('usuario_id');
                if ($sid) {
                    $u = Usuario::find($sid);
                    if ($u) {
                        $userId = $u->id_usuario;
                        $userCorreo = $u->correo ?? $u->email ?? null;
                    }
                }
            } catch (\Throwable $e) {
                // silencioso
            }
        }

        return self::create([
            'id_usuario' => $userId,
            'correo_usuario' => $userCorreo,
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'id_registro' => $idRegistro,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
