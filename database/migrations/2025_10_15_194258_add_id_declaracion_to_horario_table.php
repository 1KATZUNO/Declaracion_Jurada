<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('horario') && ! Schema::hasColumn('horario', 'id_declaracion')) {
            Schema::table('horario', function (Blueprint $table) {
                $table->unsignedBigInteger('id_declaracion')->nullable()->after('id_horario');

                // FK -> declaracion.id_declaracion (corregido)
                $table->foreign('id_declaracion')
                      ->references('id_declaracion')
                      ->on('declaracion')
                      ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('horario') && Schema::hasColumn('horario', 'id_declaracion')) {
            Schema::table('horario', function (Blueprint $table) {
                $table->dropForeign(['id_declaracion']);
                $table->dropColumn('id_declaracion');
            });
        }
    }
};
