<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Formulario extends Model
{
    use LogsActivity;
    protected $table = 'formulario';
    protected $primaryKey = 'id_formulario';
    protected $fillable = ['titulo','descripcion','fecha_creacion'];

    public function declaraciones() { return $this->hasMany(Declaracion::class, 'id_formulario'); }
}


