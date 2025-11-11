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

            // Relación con sede (tabla 'sede', PK 'id_sede')
            $table->foreignId('id_sede')
                ->constrained('sede', 'id_sede')
                ->restrictOnDelete();

            // Estado funcional de la unidad
            $table->enum('estado', ['ACTIVA', 'INACTIVA'])
                ->default('ACTIVA');

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
