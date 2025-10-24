<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('unidad_academica', function (Blueprint $t) {
            if (!Schema::hasColumn('unidad_academica', 'estado')) {
                $t->enum('estado', ['ACTIVA','INACTIVA'])->default('ACTIVA')->after('id_sede');
            }
            if (!Schema::hasColumn('unidad_academica', 'deleted_at')) {
                $t->softDeletes();
            }
            $t->index(['nombre','id_sede']);
        });
    }

    public function down(): void {
        Schema::table('unidad_academica', function (Blueprint $t) {
            if (Schema::hasColumn('unidad_academica', 'deleted_at')) {
                $t->dropSoftDeletes();
            }
            if (Schema::hasColumn('unidad_academica', 'estado')) {
                $t->dropColumn('estado');
            }
            $t->dropIndex(['unidad_academica_nombre_id_sede_index']);
        });
    }
};
