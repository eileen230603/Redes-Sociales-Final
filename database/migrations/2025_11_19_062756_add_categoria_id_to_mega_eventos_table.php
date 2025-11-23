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
        Schema::table('mega_eventos', function (Blueprint $table) {
            // Agregar columna categoria_id (nullable para migración gradual)
            $table->foreignId('categoria_id')->nullable()->after('categoria')->constrained('categorias_mega_eventos')->onDelete('set null');
            
            // Mantener categoria por compatibilidad durante migración
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mega_eventos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
    }
};
