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
        Schema::create('documento', function (Blueprint $table) {
            $table->id('id_documento'); // Clave primaria
            // CORRECCIÓN: Clave foránea que referencia ID personalizada
            $table->foreignId('id_declaracion')->constrained('declaracion', 'id_declaracion')->onDelete('cascade');
            
            $table->string('archivo', 255);
            $table->enum('formato', ['EXCEL', 'PDF']);
            $table->dateTime('fecha_generacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento');
    }
};
