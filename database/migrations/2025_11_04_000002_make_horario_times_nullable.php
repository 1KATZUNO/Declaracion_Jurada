<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite no soporta MODIFY ni ENUM, usar recreación de columnas
            Schema::table('horario', function (Blueprint $table) {
                // Eliminar columnas existentes
                $table->dropColumn(['dia', 'hora_inicio', 'hora_fin']);
            });
            
            Schema::table('horario', function (Blueprint $table) {
                // Recrear columnas como nullable
                $table->string('dia')->nullable(); // SQLite no soporta ENUM
                $table->time('hora_inicio')->nullable();
                $table->time('hora_fin')->nullable();
            });
        } else {
            // Para MySQL - usar las sentencias originales
            DB::statement("ALTER TABLE `horario` MODIFY `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NULL");
            DB::statement("ALTER TABLE `horario` MODIFY `hora_inicio` TIME NULL");
            DB::statement("ALTER TABLE `horario` MODIFY `hora_fin` TIME NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: recrear columnas como NOT NULL
            Schema::table('horario', function (Blueprint $table) {
                $table->dropColumn(['dia', 'hora_inicio', 'hora_fin']);
            });
            
            Schema::table('horario', function (Blueprint $table) {
                $table->string('dia'); // No nullable
                $table->time('hora_inicio');
                $table->time('hora_fin');
            });
        } else {
            // Para MySQL
            DB::statement("ALTER TABLE `horario` MODIFY `dia` ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NOT NULL");
            DB::statement("ALTER TABLE `horario` MODIFY `hora_inicio` TIME NOT NULL");
            DB::statement("ALTER TABLE `horario` MODIFY `hora_fin` TIME NOT NULL");
        }
    }
};
