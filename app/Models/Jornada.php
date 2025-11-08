<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    protected $table = 'jornada';
    protected $primaryKey = 'id_jornada';

    protected $fillable = [
        'tipo',          // "1/8", "1/4", "1/2", "3/4", "TC"
        'horas_por_semana',
        'activo',
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_jornada', 'id_jornada');
    }
}