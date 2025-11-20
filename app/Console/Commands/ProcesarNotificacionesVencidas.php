<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notificacion;
use Carbon\Carbon;

class ProcesarNotificacionesVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:procesar-vencidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca notificaciones vencidas después de 7 días y las elimina después de 8 días';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ahora = Carbon::now();
        
        // 1. Marcar como vencidas las notificaciones de más de 7 días
        $hace7Dias = $ahora->copy()->subDays(7);
        $notificacionesVencidas = Notificacion::where('vencida', false)
            ->where('created_at', '<=', $hace7Dias)
            ->update([
                'vencida' => true,
                'fecha_vencimiento' => $ahora
            ]);
        
        $this->info("✓ Marcadas {$notificacionesVencidas} notificaciones como vencidas (>7 días)");
        
        // 2. Eliminar notificaciones de más de 8 días
        $hace8Dias = $ahora->copy()->subDays(8);
        $notificacionesEliminadas = Notificacion::where('created_at', '<=', $hace8Dias)
            ->delete();
        
        $this->info("✓ Eliminadas {$notificacionesEliminadas} notificaciones antiguas (>8 días)");
        
        $this->info("Proceso completado exitosamente.");
        
        return Command::SUCCESS;
    }
}
