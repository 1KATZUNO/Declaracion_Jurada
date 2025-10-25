<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidad_academica', function (Blueprint $table) {
            // Campo 'estado' compatible con SQLite
            if (!Schema::hasColumn('unidad_academica', 'estado')) {
                $table->string('estado', 10)  // varchar compatible con SQLite
                      ->default('ACTIVA');
                $table->index('estado');
            }

            // Soft deletes
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
