<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horario';
    protected $primaryKey = 'id_horario';
    protected $fillable = ['id_declaracion','dia','hora_inicio','hora_fin'];

    public function declaracion() { return $this->belongsTo(Declaracion::class, 'id_declaracion'); }
}

