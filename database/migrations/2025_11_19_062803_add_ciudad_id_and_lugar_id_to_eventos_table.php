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
        Schema::table('eventos', function (Blueprint $table) {
            // Agregar ciudad_id y lugar_id
            $table->foreignId('ciudad_id')->nullable()->after('ciudad')->constrained('ciudades')->onDelete('set null');
            $table->foreignId('lugar_id')->nullable()->after('ciudad_id')->constrained('lugares')->onDelete('set null');
            
            // Mantener ciudad y direccion por compatibilidad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropForeign(['ciudad_id']);
            $table->dropForeign(['lugar_id']);
            $table->dropColumn(['ciudad_id', 'lugar_id']);
        });
    }
};
