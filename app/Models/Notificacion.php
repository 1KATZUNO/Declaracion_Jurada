<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';
    protected $primaryKey = 'id_notificacion';
    protected $fillable = ['id_usuario','mensaje','fecha_envio','estado'];

    public function usuario() { return $this->belongsTo(Usuario::class, 'id_usuario'); }
}

