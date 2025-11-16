<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividad_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('actividad_logs', 'correo_usuario')) {
                $table->string('correo_usuario', 191)->nullable()->after('id_usuario');
            }
        });
    }

    public function down(): void
    {
        Schema::table('actividad_logs', function (Blueprint $table) {
            if (Schema::hasColumn('actividad_logs', 'correo_usuario')) {
                $table->dropColumn('correo_usuario');
            }
        });
    }
};
