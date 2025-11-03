<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';
    protected $primaryKey = 'id_horario';

    /**
     * Campos asignables.
     * Nota: se agrega id_jornada (FK a tabla jornada).
     */
    protected $fillable = [
        'id_declaracion',
        'id_jornada',     // <--- NUEVO: FK a jornada
        'tipo',           // ucr | externo
        'dia',
        'hora_inicio',
        'hora_fin',
        // opcionales para "otras instituciones"
        'lugar',
        'cargo',
        'jornada',        // (texto/porcentaje histórico; puedes retirarlo cuando dejes solo la FK)
        'desde',
        'hasta',
    ];

    /**
     * Relaciones
     */
    public function declaracion()
    {
        return $this->belongsTo(Declaracion::class, 'id_declaracion', 'id_declaracion');
    }

    public function jornada()
    {
        return $this->belongsTo(Jornada::class, 'id_jornada', 'id_jornada');
    }

    /**
     * Scopes útiles
     */
    public function scopeUcr($query)
    {
        return $query->where('tipo', 'ucr');
    }

    public function scopeExterno($query)
    {
        return $query->where('tipo', 'externo');
    }

    public function scopeDia($query, string $dia)
    {
        return $query->where('dia', $dia);
    }

    /**
     * Atributos calculados / helpers
     */

    // Devuelve minutos de duración del bloque horario (asumiendo formato H:i o H:i:s)
    public function getDuracionMinutosAttribute(): int
    {
        $hi = $this->parseHora($this->hora_inicio);
        $hf = $this->parseHora($this->hora_fin);
        return $hf->diffInMinutes($hi);
    }

    // Devuelve "HH:mm" de duración (ej. "02:30")
    public function getDuracionHumanaAttribute(): string
    {
        $min = $this->duracion_minutos;
        $h = intdiv($min, 60);
        $m = $min % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    /**
     * Utilidad privada para parsear hora (H:i o H:i:s) a Carbon
     */
    protected function parseHora(?string $valor): Carbon
    {
        if (!$valor) {
            // fallback para evitar errores
            return Carbon::createFromTime(0, 0, 0);
        }

        // Acepta "H:i" o "H:i:s"
        $format = strlen($valor) === 5 ? 'H:i' : 'H:i:s';
        return Carbon::createFromFormat($format, $valor);
    }
}

