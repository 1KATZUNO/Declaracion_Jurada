<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Declaracion extends Model
{
    protected $fillable = [
        'user_id',
        'formulario_id',
        'data',
        'estado',
        'archivo',
    ];

    protected $casts = [
        'data' => 'array', // se almacena como JSON
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'formulario_id');
    }
}
