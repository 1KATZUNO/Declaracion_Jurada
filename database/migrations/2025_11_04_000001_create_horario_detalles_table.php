<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horario_detalle', function (Blueprint $table) {
            $table->id('id_detalle'); // PK
            $table->unsignedBigInteger('id_horario');
            $table->enum('dia', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();

            $table->foreign('id_horario')->references('id_horario')->on('horario')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario_detalle');
    }
};
