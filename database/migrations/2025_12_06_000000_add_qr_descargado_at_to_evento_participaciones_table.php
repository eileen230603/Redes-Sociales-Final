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
        Schema::table('evento_participaciones', function (Blueprint $table) {
            // Timestamp para registrar cuando se descargó el QR por primera vez
            // Nota: PostgreSQL no soporta 'after()', la columna se agregará al final
            if (!Schema::hasColumn('evento_participaciones', 'qr_descargado_at')) {
                $table->timestamp('qr_descargado_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evento_participaciones', function (Blueprint $table) {
            $table->dropColumn('qr_descargado_at');
        });
    }
};
