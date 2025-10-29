<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    // agrego campos de identificación comunes al fillable
    protected $fillable = ['nombre','apellido','correo','contrasena','telefono','rol', 'identificacion', 'cedula', 'numero_identificacion'];
    protected $hidden = ['contrasena'];

    // Incluir nombre completo al serializar el modelo (array/json)
    protected $appends = ['nombre_completo'];

    public function declaraciones() { return $this->hasMany(Declaracion::class, 'id_usuario'); }
    public function notificaciones() { return $this->hasMany(Notificacion::class, 'id_usuario'); }

    // Nuevo accessor: normaliza distintos nombres de campo para devolver siempre la identificación
    public function getIdentificacionAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        $candidates = ['identificacion', 'cedula', 'numero_identificacion', 'dni', 'ci'];
        foreach ($candidates as $key) {
            if (isset($this->attributes[$key]) && $this->attributes[$key] !== '') {
                return $this->attributes[$key];
            }
        }

        return null;
    }

    // Accessor para el nombre completo
    public function getNombreCompletoAttribute()
    {
        $nombre = trim(($this->attributes['nombre'] ?? '') . ' ' . ($this->attributes['apellido'] ?? ''));
        return $nombre !== '' ? $nombre : null;
    }

    // Al convertir el modelo a string (por ejemplo en vistas), devolver el nombre completo si existe
    public function __toString()
    {
        return (string) ($this->nombre_completo ?? '');
    }
}


