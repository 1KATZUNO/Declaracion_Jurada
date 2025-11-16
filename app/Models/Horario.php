<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\LogsActivity;

class Horario extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'horario';
    protected $primaryKey = 'id_horario';

    /**
     * Campos asignables.
     * Nota: se agrega id_jornada (FK a tabla jornada).
     */
    protected $fillable = [
        'id_declaracion',
        'id_jornada',     // <--- NUEVO: FK a jornada
        'id_cargo',       // <--- FK a cargo (para horarios UCR)
        'tipo',           // ucr | externo
        'dia',
        'hora_inicio',
        'hora_fin',
        // opcionales para "otras instituciones"
        'lugar',
        'cargo',          // texto libre para cargo en otras instituciones
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

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo', 'id_cargo');
    }

    // Nueva relación: detalles (intervalos) asociados al horario padre
    public function detalles()
    {
        return $this->hasMany(HorarioDetalle::class, 'id_horario', 'id_horario');
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

    // Devuelve minutos totales: suma de detalles si existen, si no cae al cálculo por campos directos
    public function getTotalMinutosAttribute(): int
    {
        if ($this->detalles()->exists()) {
            $sum = 0;
            foreach ($this->detalles as $d) {
                $hi = $this->parseHora($d->hora_inicio);
                $hf = $this->parseHora($d->hora_fin);
                $sum += $hf->diffInMinutes($hi);
            }
            return $sum;
        }

        // Fallback al comportamiento anterior (fila simple)
        $hi = $this->parseHora($this->hora_inicio ?? '00:00');
        $hf = $this->parseHora($this->hora_fin ?? '00:00');
        return $hf->diffInMinutes($hi);
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

