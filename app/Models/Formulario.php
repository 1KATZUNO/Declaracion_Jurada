<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    protected $table = 'formulario';
    protected $primaryKey = 'id_formulario';
    protected $fillable = ['titulo','descripcion','fecha_creacion'];

    public function declaraciones() { return $this->hasMany(Declaracion::class, 'id_formulario'); }
}


