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
            // Agregar id_cargo como FK nullable (solo para horarios UCR)
            $table->unsignedBigInteger('id_cargo')->nullable()->after('id_jornada');
            $table->foreign('id_cargo')->references('id_cargo')->on('cargo')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horario', function (Blueprint $table) {
            $table->dropForeign(['id_cargo']);
            $table->dropColumn('id_cargo');
        });
    }
};
