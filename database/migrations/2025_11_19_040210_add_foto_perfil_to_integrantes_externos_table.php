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
        Schema::table('integrantes_externos', function (Blueprint $table) {
            $table->string('foto_perfil', 500)->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integrantes_externos', function (Blueprint $table) {
            $table->dropColumn('foto_perfil');
        });
    }
};
