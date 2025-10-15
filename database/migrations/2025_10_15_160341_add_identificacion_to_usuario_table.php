<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('usuario', function (Blueprint $table) {
        $table->string('identificacion', 20)->nullable()->after('apellido');
    });
}

public function down()
{
    Schema::table('usuario', function (Blueprint $table) {
        $table->dropColumn('identificacion');
    });
}
};
