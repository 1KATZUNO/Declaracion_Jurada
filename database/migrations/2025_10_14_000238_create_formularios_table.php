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
        Schema::create('formulario', function (Blueprint $table) {
            $table->id('id_formulario'); // Clave primaria
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->date('fecha_creacion')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formularios');
    }
};
