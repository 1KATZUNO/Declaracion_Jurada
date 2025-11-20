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
        // Agregar id_usuario a sede
        Schema::table('sede', function (Blueprint $table) {
            $table->foreignId('id_usuario')->nullable()->after('id_sede')->constrained('usuario', 'id_usuario')->onDelete('cascade');
        });

        // Agregar id_usuario a unidad_academica
        Schema::table('unidad_academica', function (Blueprint $table) {
            $table->foreignId('id_usuario')->nullable()->after('id_unidad')->constrained('usuario', 'id_usuario')->onDelete('cascade');
        });

        // Agregar id_usuario a cargo
        Schema::table('cargo', function (Blueprint $table) {
            $table->foreignId('id_usuario')->nullable()->after('id_cargo')->constrained('usuario', 'id_usuario')->onDelete('cascade');
        });

        // Agregar id_usuario a formulario
        Schema::table('formulario', function (Blueprint $table) {
            $table->foreignId('id_usuario')->nullable()->after('id_formulario')->constrained('usuario', 'id_usuario')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sede', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('id_usuario');
        });

        Schema::table('unidad_academica', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('id_usuario');
        });

        Schema::table('cargo', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('id_usuario');
        });

        Schema::table('formulario', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('id_usuario');
        });
    }
};
