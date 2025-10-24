<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadAcademica extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'unidad_academica';
    protected $primaryKey = 'id_unidad';

    protected $fillable = [
        'nombre',
        'id_sede',
        'estado', // ACTIVA | INACTIVA (opcional que lo envíes, la BD puede poner default)
    ];

    public function sede() { return $this->belongsTo(Sede::class, 'id_sede'); }
    public function declaraciones() { return $this->hasMany(Declaracion::class, 'id_unidad'); }
}
