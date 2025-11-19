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
            if (!Schema::hasColumn('declaracion', 'identificacion')) {
                $table->string('identificacion', 50)->nullable()->after('id_usuario');
            }
            if (!Schema::hasColumn('declaracion', 'telefono')) {
                $table->string('telefono', 50)->nullable()->after('identificacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declaracion', function (Blueprint $table) {
            if (Schema::hasColumn('declaracion', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('declaracion', 'identificacion')) {
                $table->dropColumn('identificacion');
            }
        });
    }
};
