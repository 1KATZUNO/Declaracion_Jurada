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
        Schema::create('horario', function (Blueprint $table) {
            $table->id('id_horario'); // Clave primaria$table->foreignId('id_declaracion')->constrained('declaracion')->onDelete('cascade');
            // CORRECCIÓN: Clave foránea que referencia ID personalizada
            $table->foreignId('id_declaracion')->constrained('declaracion', 'id_declaracion')->onDelete('cascade');
            
            $table->enum('dia', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
            $table->enum('tipo', ['ucr', 'externo'])->default('ucr');
            $table->string('lugar')->nullable();
        });
    }

    /**
     * Reverse the migrations.x
     */
    public function down(): void
    {
        Schema::dropIfExists('horario');
    }
};
