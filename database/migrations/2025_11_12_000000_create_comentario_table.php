<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comentario', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->foreignId('id_usuario')->constrained('usuario','id_usuario')->cascadeOnDelete();
            $table->string('titulo', 200)->nullable();
            $table->text('mensaje');
            $table->enum('estado', ['abierto','cerrado'])->default('abierto');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('comentario');
    }
};

