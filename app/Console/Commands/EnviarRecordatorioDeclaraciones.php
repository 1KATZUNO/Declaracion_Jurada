<?php

namespace App\Console\Commands;

use App\Models\Declaracion;
use App\Models\Usuario;
use App\Notifications\RecordatorioPresentarDeclaracion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class EnviarRecordatorioDeclaraciones extends Command
{
    protected $signature = 'declaraciones:recordatorio {--dias=7 : Días de anticipación para avisar que la declaración vence}';

    protected $description = 'Envía recordatorios por cada Declaración Jurada próxima a vencer.';

    public function handle(): int
    {
        $dias = (int) $this->option('dias');
        $limite = Carbon::now()->startOfDay()->addDays($dias);

        Usuario::query()
            ->with(['declaraciones' => function ($query) {
                $query->orderByDesc('fecha_hasta');
            }])
            ->orderBy('id_usuario')
            ->chunkById(100, function (Collection $usuarios) use ($limite) {
                $usuarios->each(function (Usuario $usuario) use ($limite) {

                    foreach ($usuario->declaraciones as $declaracion) {
                        $fechaLimite = $this->obtenerFechaLimite($declaracion);

                        // Omitir si no tiene fecha o si no está dentro del rango
                        if (! $this->debeNotificarse($fechaLimite, $limite)) {
                            continue;
                        }

                        // Evitar duplicados recientes para esta misma declaración
                        if ($this->notificadoRecientemente($usuario, $fechaLimite, $declaracion->id_declaracion)) {
                            continue;
                        }

                        // Enviar notificación individual por esta declaración
                        $usuario->notify(new RecordatorioPresentarDeclaracion($fechaLimite, $declaracion));

                        $this->info("Notificación enviada por la declaración #{$declaracion->id_declaracion} del usuario {$usuario->nombre_completo}");
                    }

                });
            }, 'id_usuario');

        return self::SUCCESS;
    }

    protected function obtenerFechaLimite(?Declaracion $declaracion): ?Carbon
    {
        if (! $declaracion || empty($declaracion->fecha_hasta)) {
            return null;
        }

        return Carbon::parse($declaracion->fecha_hasta)->startOfDay();
    }

    protected function debeNotificarse(?Carbon $fechaLimite, Carbon $limite): bool
    {
        if (! $fechaLimite) {
            return false;
        }

        return $fechaLimite->lessThanOrEqualTo($limite);
    }

    protected function notificadoRecientemente(Usuario $usuario, ?Carbon $fechaLimite, ?int $idDeclaracion = null): bool
    {
        $query = $usuario->notifications()
            ->where('type', RecordatorioPresentarDeclaracion::class)
            ->where('created_at', '>=', Carbon::now()->subDay());

        if ($fechaLimite) {
            $query->whereJsonContains('data->fecha_limite', $fechaLimite->toDateString());
        }

        if ($idDeclaracion) {
            $query->whereJsonContains('data->declaracion_id', $idDeclaracion);
        }

        return $query->exists();
    }
}
