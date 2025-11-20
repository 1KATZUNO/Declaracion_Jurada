<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Notificacion extends Model
{
    use LogsActivity;
    protected $table = 'notificacion';
    protected $primaryKey = 'id_notificacion';
    protected $fillable = [
        'id_usuario', 
        'titulo',
        'mensaje', 
        'tipo',
        'id_declaracion',
        'fecha_envio', 
        'estado',
        'leida',
        'fecha_lectura',
        'fecha_vencimiento',
        'vencida'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_lectura' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'leida' => 'boolean',
        'vencida' => 'boolean',
    ];

    // Constantes para tipos de notificación
    const TIPO_CREAR = 'crear';
    const TIPO_EDITAR = 'editar';
    const TIPO_ELIMINAR = 'eliminar';
    const TIPO_EXPORTAR = 'exportar';
    const TIPO_VENCIMIENTO = 'vencimiento';

    // Relaciones
    public function usuario() 
    { 
        return $this->belongsTo(Usuario::class, 'id_usuario'); 
    }

    public function declaracion() 
    { 
        return $this->belongsTo(Declaracion::class, 'id_declaracion'); 
    }

    // Scopes
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    
    public function scopeVigentes($query)
    {
        return $query->where('vencida', false);
    }
    
    public function scopeVencidas($query)
    {
        return $query->where('vencida', true);
    }

    // Métodos de utilidad
    public function marcarComoLeida()
    {
        $this->update(['leida' => true]);
    }
}

