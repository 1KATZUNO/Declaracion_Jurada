<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'telefono',
        'rol',
        'identificacion'
    ];

    protected $hidden = ['contrasena'];
    protected $appends = ['nombre_completo'];

    public function declaraciones()
    {
        return $this->hasMany(Declaracion::class, 'id_usuario');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario');
    }

    // Accessor: devuelve la identificación
    public function getIdentificacionAttribute($value)
    {
        return $value ?? null;
    }

    // Accessor: nombre completo
    public function getNombreCompletoAttribute()
    {
        $nombre = trim(($this->attributes['nombre'] ?? '') . ' ' . ($this->attributes['apellido'] ?? ''));
        return $nombre !== '' ? $nombre : null;
    }

    public function __toString()
    {
        return (string)($this->nombre_completo ?? '');
    }
}
