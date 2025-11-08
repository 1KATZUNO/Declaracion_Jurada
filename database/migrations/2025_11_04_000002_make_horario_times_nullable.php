<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Modificar columnas para aceptar NULL (se usa SQL directo para evitar dependencia de doctrine/dbal)
        DB::statement("ALTER TABLE `horario` MODIFY `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_inicio` TIME NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_fin` TIME NULL");
    }

    public function down(): void
    {
        // Volver a NOT NULL (si tu entorno tiene datos, revisar antes)
        DB::statement("ALTER TABLE `horario` MODIFY `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NOT NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_inicio` TIME NOT NULL");
        DB::statement("ALTER TABLE `horario` MODIFY `hora_fin` TIME NOT NULL");
    }
};
