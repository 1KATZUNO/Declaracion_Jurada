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
        Schema::create('actividad_logs', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->foreignId('id_usuario')->nullable()->constrained('usuario', 'id_usuario')->onDelete('set null');
            $table->string('accion'); // crear, editar, eliminar, exportar, login, logout, etc.
            $table->string('modulo'); // declaracion, usuario, cargo, etc.
            $table->string('descripcion'); // descripción legible de la actividad
            $table->unsignedBigInteger('id_registro')->nullable(); // ID del registro afectado
            $table->json('datos_anteriores')->nullable(); // datos antes del cambio
            $table->json('datos_nuevos')->nullable(); // datos después del cambio
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Índices para mejorar búsquedas
            $table->index(['id_usuario', 'created_at']);
            $table->index(['accion']);
            $table->index(['modulo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividad_logs');
    }
};
