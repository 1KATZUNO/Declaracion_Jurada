<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horario', function (Blueprint $table) {
            try {
                $table->dropForeign(['id_declaracion']);
            } catch (\Throwable $e) {
                // ignorar si no existe
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

        if ($driver === 'sqlite') {
            $tieneJornada = Schema::hasColumn('horario', 'id_jornada');
            Schema::disableForeignKeyConstraints();

            $registros = DB::table('horario')->get();

            Schema::create('horario_temp', function (Blueprint $table) use ($tieneJornada) {
                $table->id('id_horario');
                $table->unsignedBigInteger('id_declaracion')->nullable();
                $table->enum('dia', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->timestamps();
                $table->enum('tipo', ['ucr', 'externo'])->default('ucr');
                $table->string('lugar')->nullable();

                if ($tieneJornada) {
                    $table->unsignedBigInteger('id_jornada')->nullable();
                }

                $table->foreign('id_declaracion')
                      ->references('id_declaracion')
                      ->on('declaracion')
                      ->onDelete('cascade');

                if ($tieneJornada) {
                    $table->foreign('id_jornada')
                          ->references('id_jornada')
                          ->on('jornada');
                }
            });

            foreach ($registros as $registro) {
                $datos = (array) $registro;
                if (!$tieneJornada) {
                    unset($datos['id_jornada']);
                }
                DB::table('horario_temp')->insert($datos);
            }

            Schema::drop('horario');
            Schema::rename('horario_temp', 'horario');
            Schema::enableForeignKeyConstraints();
        } else {
            // ✅ Solo ejecutar en MySQL (no en SQLite)
            if ($driver !== 'sqlite') {
                DB::statement('ALTER TABLE `horario` MODIFY `id_declaracion` BIGINT UNSIGNED NULL');
            }

            Schema::table('horario', function (Blueprint $table) {
                if (!Schema::hasColumn('horario', 'id_declaracion')) {
                    $table->unsignedBigInteger('id_declaracion')->nullable()->after('id_horario');
                }

                $table->foreign('id_declaracion')
                      ->references('id_declaracion')
                      ->on('declaracion')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('horario', function (Blueprint $table) {
            try {
                $table->dropForeign(['id_declaracion']);
            } catch (\Throwable $e) {
                // ignorar si no existe
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

        if ($driver === 'sqlite') {
            $tieneJornada = Schema::hasColumn('horario', 'id_jornada');
            Schema::disableForeignKeyConstraints();

            $registros = DB::table('horario')->get();

            Schema::create('horario_temp', function (Blueprint $table) use ($tieneJornada) {
                $table->id('id_horario');
                $table->unsignedBigInteger('id_declaracion');
                $table->enum('dia', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->timestamps();
                $table->enum('tipo', ['ucr', 'externo'])->default('ucr');
                $table->string('lugar')->nullable();

                if ($tieneJornada) {
                    $table->unsignedBigInteger('id_jornada')->nullable();
                }

                $table->foreign('id_declaracion')
                      ->references('id_declaracion')
                      ->on('declaracion')
                      ->onDelete('cascade');

                if ($tieneJornada) {
                    $table->foreign('id_jornada')
                          ->references('id_jornada')
                          ->on('jornada');
                }
            });

            foreach ($registros as $registro) {
                $datos = (array) $registro;
                if (!$tieneJornada) {
                    unset($datos['id_jornada']);
                }
                DB::table('horario_temp')->insert($datos);
            }

            Schema::drop('horario');
            Schema::rename('horario_temp', 'horario');
            Schema::enableForeignKeyConstraints();
        } else {
            // ✅ Solo ejecutar en MySQL (no en SQLite)
            if ($driver !== 'sqlite') {
                DB::statement('ALTER TABLE `horario` MODIFY `id_declaracion` BIGINT UNSIGNED NOT NULL');
            }

            Schema::table('horario', function (Blueprint $table) {
                $table->foreign('id_declaracion')
                      ->references('id_declaracion')
                      ->on('declaracion')
                      ->onDelete('cascade');
            });
        }
    }
};
