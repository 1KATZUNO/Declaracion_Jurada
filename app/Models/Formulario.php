<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    protected $fillable = ['titulo', 'descripcion', 'campos'];

    protected $casts = [
        'campos' => 'array'
    ];
}

