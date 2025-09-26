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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // Nombre completo
            $table->string('email')->unique();            // Correo
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');                   // Contraseña
            $table->rememberToken();

            // Campos extra para nuestro sistema
            $table->string('role')->default('profesor');  // profesor o administrador
            $table->string('cedula')->nullable();         // cédula de identidad
            $table->string('departamento')->nullable();   // departamento académico
            $table->string('telefono')->nullable();       // número de teléfono

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
