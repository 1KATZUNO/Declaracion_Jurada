<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
    use Notifiable; // Permite enviar y recibir notificaciones (correo y base de datos)

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
    //Con este metodo se utiliza el notification nativo de laravel
    public function routeNotificationForMail()
    {
        return $this->correo;
    }
    public function getIdentificacionAttribute($value)
    {
        return $value ?? null;
    }

    public function getNombreCompletoAttribute()
    {
        $nombre = trim(($this->attributes['nombre'] ?? '') . ' ' . ($this->attributes['apellido'] ?? ''));
        return $nombre !== '' ? $nombre : null;
    }
    // CONVERSIÓN A TEXTO
    public function __toString()
    {
        return (string)($this->nombre_completo ?? '');
    }
}
