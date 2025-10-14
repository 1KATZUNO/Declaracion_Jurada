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
        Schema::create('notificacion', function (Blueprint $table) {
            $table->id('id_notificacion'); // Clave primaria
            // CORRECCIÓN: Clave foránea que referencia ID personalizada
            $table->foreignId('id_usuario')->constrained('usuario', 'id_usuario')->onDelete('cascade');
            
            $table->text('mensaje');
            $table->dateTime('fecha_envio')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('estado', ['pendiente', 'enviada', 'leída'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
