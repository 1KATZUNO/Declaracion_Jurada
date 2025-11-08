<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jornada', function (Blueprint $table) {
            $table->id('id_jornada');
            $table->string('tipo', 20);               // Ej: 1/8, 1/4, 1/2, 3/4, TC
            $table->unsignedTinyInteger('horas_por_semana'); // Ej: 5, 10, 20, 30, 40
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jornada');
    }
};

