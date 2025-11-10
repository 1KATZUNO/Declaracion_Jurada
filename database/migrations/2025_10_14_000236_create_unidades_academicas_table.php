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
        Schema::create('unidad_academica', function (Blueprint $table) {
            $table->id('id_unidad'); // Clave primaria
            $table->string('nombre', 100);
            // CORRECCIÓN: constrained('tabla', 'nombre_de_la_id_en_esa_tabla')
            $table->foreignId('id_sede')->constrained('sede', 'id_sede')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidad_academica');
    }
};
