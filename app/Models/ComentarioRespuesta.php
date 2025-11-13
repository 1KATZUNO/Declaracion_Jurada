<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioRespuesta extends Model
{
    protected $table = 'comentario_respuesta';
    protected $primaryKey = 'id_respuesta';
    protected $fillable = ['id_comentario','id_usuario','mensaje'];

    public function comentario()
    {
        return $this->belongsTo(Comentario::class, 'id_comentario', 'id_comentario');
    }

    public function autor()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
