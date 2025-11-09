<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Intentar quitar la FK existente (si la hay), luego modificar la columna con SQL
        Schema::table('horario', function (Blueprint $table) {
            // dropForeign puede lanzar si no existe; envolver en try/catch
            try {
                $table->dropForeign(['id_declaracion']);
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // Para SQLite, necesitamos recrear la tabla ya que no soporta MODIFY
        if (DB::getDriverName() === 'sqlite') {
            // En SQLite, recrear la columna
            Schema::table('horario', function (Blueprint $table) {
                $table->dropColumn('id_declaracion');
            });
            Schema::table('horario', function (Blueprint $table) {
                $table->unsignedBigInteger('id_declaracion')->nullable()->after('id_horario');
            });
        } else {
            // Para MySQL
            DB::statement('ALTER TABLE `horario` MODIFY `id_declaracion` BIGINT UNSIGNED NULL');
        }

        // Re-crear la FK apuntando a declaracion.id_declaracion
        Schema::table('horario', function (Blueprint $table) {
            // asegúrate que la columna existe y sea unsignedBigInteger
            if (!Schema::hasColumn('horario', 'id_declaracion')) {
                $table->unsignedBigInteger('id_declaracion')->nullable()->after('id_horario');
            }
            $table->foreign('id_declaracion')->references('id_declaracion')->on('declaracion')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Para revertir: quitar FK, hacer NOT NULL y volver a crear FK
        Schema::table('horario', function (Blueprint $table) {
            try {
                $table->dropForeign(['id_declaracion']);
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // Para SQLite, necesitamos recrear la tabla ya que no soporta MODIFY
        if (DB::getDriverName() === 'sqlite') {
            // En SQLite, recrear la columna
            Schema::table('horario', function (Blueprint $table) {
                $table->dropColumn('id_declaracion');
            });
            Schema::table('horario', function (Blueprint $table) {
                $table->unsignedBigInteger('id_declaracion')->after('id_horario'); // NOT NULL
            });
        } else {
            // Para MySQL
            DB::statement('ALTER TABLE `horario` MODIFY `id_declaracion` BIGINT UNSIGNED NOT NULL');
        }

        Schema::table('horario', function (Blueprint $table) {
            $table->foreign('id_declaracion')->references('id_declaracion')->on('declaracion')->onDelete('cascade');
        });
    }
};
