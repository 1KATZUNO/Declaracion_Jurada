<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documento';
    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'id_declaracion',
        'archivo',
        'formato',
        'fecha_generacion'
    ];

    public function declaracion()
    {
        return $this->belongsTo(Declaracion::class, 'id_declaracion', 'id_declaracion');
    }
}
