<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';
    protected $primaryKey = 'id_horario';

   protected $fillable = [
    'id_declaracion',
    'tipo',
    'dia',
    'hora_inicio',
    'hora_fin',
    // campos opcionales para "otras instituciones"
    'lugar',
    'cargo',
    'jornada',
    'desde',
    'hasta',
];


    public function declaracion()
    {
        return $this->belongsTo(Declaracion::class, 'id_declaracion', 'id_declaracion');
    }
}
