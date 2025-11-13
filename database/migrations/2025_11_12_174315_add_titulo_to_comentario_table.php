<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comentario', function (Blueprint $table) {
            $table->string('titulo', 200)->nullable()->after('id_usuario');
        });
    }

    public function down(): void
    {
        Schema::table('comentario', function (Blueprint $table) {
            $table->dropColumn('titulo');
        });
    }
};
