<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Declaracion extends Model
{
    protected $table = 'declaracion';
    protected $primaryKey = 'id_declaracion';
    protected $fillable = [
        'id_usuario','id_formulario','id_unidad','id_cargo',
        'fecha_desde','fecha_hasta','horas_totales','fecha_envio'
    ];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario'); }
    public function formulario() { return $this->belongsTo(Formulario::class, 'id_formulario'); }
    public function unidad() { return $this->belongsTo(UnidadAcademica::class, 'id_unidad'); }
    public function cargo() { return $this->belongsTo(Cargo::class, 'id_cargo'); }
    public function horarios() { return $this->hasMany(Horario::class, 'id_declaracion'); }
    public function documentos() { return $this->hasMany(Documento::class, 'id_declaracion'); }
}


