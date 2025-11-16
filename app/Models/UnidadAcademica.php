<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class UnidadAcademica extends Model
{
    use LogsActivity;
    protected $table = 'unidad_academica';
    protected $primaryKey = 'id_unidad';
    protected $fillable = ['nombre','id_sede','estado'];

    public function sede() { return $this->belongsTo(Sede::class, 'id_sede'); }
    public function declaraciones() { return $this->hasMany(Declaracion::class, 'id_unidad'); }
}

