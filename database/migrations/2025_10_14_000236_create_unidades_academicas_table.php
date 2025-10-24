<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidad_academica', function (Blueprint $table) {
            if (!Schema::hasColumn('unidad_academica', 'estado')) {
                // enum con default ACTIVA
                $table->enum('estado', ['ACTIVA','INACTIVA'])
                      ->default('ACTIVA')
                      ->after('id_sede');
                $table->index('estado');
            }

            if (!Schema::hasColumn('unidad_academica', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('unidad_academica', function (Blueprint $table) {
            if (Schema::hasColumn('unidad_academica', 'estado')) {
                $table->dropIndex(['estado']);
                $table->dropColumn('estado');
            }
            if (Schema::hasColumn('unidad_academica', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
