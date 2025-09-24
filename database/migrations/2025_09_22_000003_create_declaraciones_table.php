<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('declaraciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('formulario_id')->nullable()->constrained()->nullOnDelete();
            $table->json('data');
            $table->string('estado')->default('generada'); // generada, firmada, enviada
            $table->string('archivo')->nullable(); // ruta al excel generado
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('declaraciones');
    }
};

