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
        Schema::table('declaracion', function (Blueprint $table) {
            // Hacer nullable el campo id_cargo ya que ahora se maneja por horario
            $table->unsignedBigInteger('id_cargo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declaracion', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cargo')->nullable(false)->change();
        });
    }
};
