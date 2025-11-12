<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Recordatorios de vencimiento - se ejecuta diariamente para buscar declaraciones que vencen en 7 días
        // MEJORADO: Ahora solo envía recordatorios para declaraciones con fecha_hasta = hoy + 7 días
        $schedule->command('notificaciones:recordatorios-vencimiento')->dailyAt('08:00');
        
        // Alternativas de configuración:
        // Para recordatorios múltiples (uncomment si se necesita):
        // $schedule->command('notificaciones:recordatorios-vencimiento --dias=3')->dailyAt('09:00'); // 3 días antes
        // $schedule->command('notificaciones:recordatorios-vencimiento --dias=1')->dailyAt('10:00'); // 1 día antes
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

