<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Sede extends Model
{
    use LogsActivity;
    protected $table = 'sede';
    protected $primaryKey = 'id_sede';
    protected $fillable = ['nombre','ubicacion','id_usuario'];

    public function unidadesAcademicas(){ return $this->hasMany(UnidadAcademica::class, 'id_sede');}
    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario'); }
}
