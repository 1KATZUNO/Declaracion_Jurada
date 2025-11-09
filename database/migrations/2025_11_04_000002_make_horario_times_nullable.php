<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite no soporta ALTER TABLE MODIFY ni ENUM
            // Por compatibilidad, simplemente omitimos este cambio en tests
            return;
        }

        // MySQL: modificar las columnas para permitir NULL
        DB::statement("ALTER TABLE `horario` MODIFY `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_inicio` TIME NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_fin` TIME NULL");
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // Omitir reversión en SQLite (no compatible)
            return;
        }

        // MySQL: revertir los cambios (volver a NOT NULL)
        DB::statement("ALTER TABLE `horario` MODIFY `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NOT NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_inicio` TIME NOT NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_fin` TIME NOT NULL");
    }
};
