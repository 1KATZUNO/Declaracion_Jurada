<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comentario_respuesta', function (Blueprint $table) {
            $table->id('id_respuesta');
            $table->foreignId('id_comentario')->constrained('comentario','id_comentario')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('usuario','id_usuario')->cascadeOnDelete();
            $table->text('mensaje');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('comentario_respuesta');
    }
};
