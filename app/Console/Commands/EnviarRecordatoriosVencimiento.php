<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Declaracion;
use App\Services\NotificacionService;
use Carbon\Carbon;

class EnviarRecordatoriosVencimiento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:recordatorios-vencimiento {--dias=7 : Días antes del vencimiento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar recordatorios a usuarios que tienen declaraciones próximas a vencer';

    protected $notificacionService;

    public function __construct()
    {
        parent::__construct();
        $this->notificacionService = new NotificacionService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $diasAntes = (int) $this->option('dias');
        $fechaObjetivo = Carbon::now()->addDays($diasAntes)->toDateString();
        
        $this->info("Buscando declaraciones que vencen el {$fechaObjetivo} ({$diasAntes} días desde hoy)...");

        // Buscar declaraciones que vencen exactamente en X días
        $declaracionesProximasVencer = Declaracion::with('usuario')
            ->where('fecha_hasta', $fechaObjetivo)
            ->whereHas('usuario', function ($query) {
                $query->where('estado', 'ACTIVO'); // Solo usuarios activos
            })
            ->get();

        $this->info("Encontradas {$declaracionesProximasVencer->count()} declaraciones próximas a vencer");

        $enviados = 0;

        foreach ($declaracionesProximasVencer as $declaracion) {
            try {
                $usuario = $declaracion->usuario;
                
                // Verificar que no se haya enviado recordatorio reciente para esta declaración
                $recordatorioReciente = \App\Models\Notificacion::where('id_usuario', $usuario->id_usuario)
                    ->where('id_declaracion', $declaracion->id_declaracion)
                    ->where('tipo', 'vencimiento')
                    ->where('created_at', '>=', Carbon::now()->subHours(6)) // No enviar si ya se envió en las últimas 6 horas
                    ->exists();

                if (!$recordatorioReciente) {
                    $this->notificacionService->notificarVencimientoDeclaracion($usuario, $declaracion, $diasAntes);
                    $enviados++;
                    $this->line("✓ Recordatorio enviado a: {$usuario->nombre} {$usuario->apellido} - Declaración vence: {$declaracion->fecha_hasta}");
                } else {
                    $this->line("⏭ Recordatorio ya enviado recientemente para: {$usuario->nombre} {$usuario->apellido}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Error enviando a {$usuario->nombre}: " . $e->getMessage());
            }
        }

        $this->info("Proceso completado. Recordatorios enviados: {$enviados}");
        
        return Command::SUCCESS;
    }
}
