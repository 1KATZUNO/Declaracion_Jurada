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
        Schema::table('notificacion', function (Blueprint $table) {
            $table->string('titulo')->after('id_usuario'); // Título de la notificación
            $table->string('tipo')->after('titulo'); // Tipo: crear, editar, eliminar, exportar, vencimiento
            $table->unsignedBigInteger('id_declaracion')->nullable()->after('tipo'); // Relación con declaración
            $table->boolean('leida')->default(false)->after('estado'); // Estado de lectura
            
            // Agregar índice para optimizar consultas
            $table->index(['id_usuario', 'leida']);
            $table->index(['tipo']);
            
            // Clave foránea para declaración (si existe)
            $table->foreign('id_declaracion')->references('id_declaracion')->on('declaracion')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificacion', function (Blueprint $table) {
            $table->dropForeign(['id_declaracion']);
            $table->dropIndex(['id_usuario', 'leida']);
            $table->dropIndex(['tipo']);
            $table->dropColumn(['titulo', 'tipo', 'id_declaracion', 'leida']);
        });
    }
};
