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

    protected $description = 'Envía recordatorios a los funcionarios que deben presentar su Declaración Jurada.';

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
                    $ultimaDeclaracion = $usuario->declaraciones->first();
                    $fechaLimite = $this->obtenerFechaLimite($ultimaDeclaracion);

                    if (! $this->debeNotificarse($fechaLimite, $limite)) {
                        return;
                    }

                    if ($this->notificadoRecientemente($usuario, $fechaLimite)) {
                        return;
                    }

                    $usuario->notify(new RecordatorioPresentarDeclaracion($fechaLimite));
                    $this->info("Notificación enviada a {$usuario->nombre_completo}");
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
            return true;
        }

        return $fechaLimite->lessThanOrEqualTo($limite);
    }

    protected function notificadoRecientemente(Usuario $usuario, ?Carbon $fechaLimite): bool
    {
        return $usuario->notifications()
            ->where('type', RecordatorioPresentarDeclaracion::class)
            ->when($fechaLimite, function ($query) use ($fechaLimite) {
                $query->whereJsonContains('data->fecha_limite', $fechaLimite->toDateString());
            })
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->exists();
    }
}

