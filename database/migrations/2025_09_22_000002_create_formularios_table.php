<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('formularios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->json('campos'); // estructura del formulario
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('formularios');
    }
};

