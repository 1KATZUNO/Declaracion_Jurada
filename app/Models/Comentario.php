<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Comentario extends Model
{
    use LogsActivity;
    protected $table = 'comentario';
    protected $primaryKey = 'id_comentario';
    protected $fillable = ['id_usuario','titulo','mensaje','estado'];

    public function autor()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function respuestas()
    {
        return $this->hasMany(ComentarioRespuesta::class, 'id_comentario', 'id_comentario')
                    ->orderBy('created_at', 'asc');
    }
}
