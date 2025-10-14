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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario'); // Clave primaria
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('correo', 100)->unique();
            $table->string('contrasena', 255);
            $table->string('telefono', 20)->nullable();
            $table->enum('rol', ['funcionario', 'admin'])->default('funcionario');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
