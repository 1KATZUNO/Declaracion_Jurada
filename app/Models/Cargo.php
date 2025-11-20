<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Cargo extends Model
{
    use LogsActivity;
    protected $table = 'cargo';
    protected $primaryKey = 'id_cargo';
    protected $fillable = ['nombre','jornada','descripcion','id_usuario'];

    public function declaraciones() { return $this->hasMany(Declaracion::class, 'id_cargo'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario'); }
}


