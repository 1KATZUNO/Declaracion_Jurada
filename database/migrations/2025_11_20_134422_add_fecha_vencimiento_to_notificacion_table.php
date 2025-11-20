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
            $table->timestamp('fecha_vencimiento')->nullable()->after('fecha_lectura');
            $table->boolean('vencida')->default(false)->after('fecha_vencimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notificacion', function (Blueprint $table) {
            $table->dropColumn(['fecha_vencimiento', 'vencida']);
        });
    }
};
