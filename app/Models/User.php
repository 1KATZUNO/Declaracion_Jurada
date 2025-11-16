<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'usuario';  // 👈 tu tabla real

    protected $primaryKey = 'id_usuario';  // 👈 tu PK real

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'apellido',
        'identificacion',
        'correo',
        'contrasena',
        'telefono',
        'rol'
    ];

    // 👇 MUY IMPORTANTE
    public function getAuthPassword() {
        return $this->contrasena;   // tu columna real
    }
}
