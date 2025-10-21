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
        Schema::create('declaracion', function (Blueprint $table) {
            $table->id('id_declaracion'); // Clave primaria
            
            // CORRECCIÓN: Claves foráneas que referencian IDs personalizadas
            $table->foreignId('id_usuario')->constrained('usuario', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_formulario')->constrained('formulario', 'id_formulario')->onDelete('cascade');
            $table->foreignId('id_unidad')->constrained('unidad_academica', 'id_unidad')->onDelete('cascade');
            $table->foreignId('id_cargo')->constrained('cargo', 'id_cargo')->onDelete('cascade');
            
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->decimal('horas_totales', 5, 2);
            $table->dateTime('fecha_envio')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaracion');
    }
};
