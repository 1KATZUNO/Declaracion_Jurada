<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('horario', function (Blueprint $table) {
            // Añadimos la FK a jornada
            $table->unsignedBigInteger('id_jornada')->nullable()->after('id_declaracion');
            $table->foreign('id_jornada')->references('id_jornada')->on('jornada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horario', function (Blueprint $table) {
            $table->dropForeign(['id_jornada']);
            $table->dropColumn('id_jornada');
        });
    }
};
