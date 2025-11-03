<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioDetalle extends Model
{
    protected $table = 'horario_detalle';
    protected $primaryKey = 'id_detalle';
    protected $fillable = [
        'id_horario',
        'dia',
        'hora_inicio',
        'hora_fin',
    ];

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario', 'id_horario');
    }
}
