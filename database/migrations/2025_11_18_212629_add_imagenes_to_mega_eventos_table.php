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
            if (!Schema::hasColumn('mega_eventos', 'imagenes')) {
                $table->json('imagenes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mega_eventos', function (Blueprint $table) {
            if (Schema::hasColumn('mega_eventos', 'imagenes')) {
                $table->dropColumn('imagenes');
            }
        });
    }
};
