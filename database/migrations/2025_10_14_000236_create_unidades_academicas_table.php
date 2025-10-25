<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Verificamos si la tabla existe, si no, la creamos
        if (!Schema::hasTable('unidad_academica')) {
            Schema::create('unidad_academica', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_sede');
                
                // Estado: usamos string en SQLite, enum en MySQL
                if (Schema::getConnection()->getDriverName() === 'sqlite') {
                    $table->string('estado')->default('ACTIVA');
                } else {
                    $table->enum('estado', ['ACTIVA', 'INACTIVA'])->default('ACTIVA');
                }

                $table->timestamps();
                $table->softDeletes();

                // Index para estado
                $table->index('estado');
            });
        } else {
            // Si la tabla ya existe, agregamos solo las columnas faltantes
            Schema::table('unidad_academica', function (Blueprint $table) {
                if (!Schema::hasColumn('unidad_academica', 'estado')) {
                    if (Schema::getConnection()->getDriverName() === 'sqlite') {
                        $table->string('estado')->default('ACTIVA');
                    } else {
                        $table->enum('estado', ['ACTIVA', 'INACTIVA'])->default('ACTIVA');
                    }
                    $table->index('estado');
                }

                if (!Schema::hasColumn('unidad_academica', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
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
