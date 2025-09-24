<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    protected $fillable = ['titulo', 'descripcion', 'campos'];

    protected $casts = [
        'campos' => 'array', // se almacena como JSON
    ];

    public function declaraciones()
    {
        return $this->hasMany(Declaracion::class, 'formulario_id');
    }
}

