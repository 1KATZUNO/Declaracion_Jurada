<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadAcademica extends Model
{
    use HasFactory;

    protected $table = 'unidad_academica';
    protected $primaryKey = 'id_unidad';

    protected $fillable = ['nombre', 'id_sede'];

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'id_sede', 'id_sede');
    }

    public function declaraciones()
    {
        return $this->hasMany(Declaracion::class, 'id_unidad', 'id_unidad');
    }
}

