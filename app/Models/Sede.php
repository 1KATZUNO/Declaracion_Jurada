<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
class Sede extends Model
{
    use HasFactory; 
    protected $table = 'sede';
    protected $primaryKey = 'id_sede';
    protected $fillable = ['nombre','ubicacion','estado']; // si ya añadiste estado
    public function unidades() { return $this->hasMany(UnidadAcademica::class, 'id_sede'); }
}