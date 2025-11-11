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
        Schema::table('declaracion', function (Blueprint $table) {
            $table->text('observaciones_adicionales')->nullable()->after('fecha_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declaracion', function (Blueprint $table) {
            $table->dropColumn('observaciones_adicionales');
        });
    }
};
