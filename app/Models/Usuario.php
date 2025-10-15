<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    protected $fillable = ['nombre','apellido','correo','contrasena','telefono','rol'];
    protected $hidden = ['contrasena'];

    public function declaraciones() { return $this->hasMany(Declaracion::class, 'id_usuario'); }
    public function notificaciones() { return $this->hasMany(Notificacion::class, 'id_usuario'); }
}


