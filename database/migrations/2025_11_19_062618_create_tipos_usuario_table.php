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
        Schema::create('tipos_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único del tipo (ej: ong, empresa, externo, admin)');
            $table->string('nombre', 100)->comment('Nombre del tipo de usuario');
            $table->text('descripcion')->nullable()->comment('Descripción del tipo');
            $table->json('permisos_default')->nullable()->comment('Permisos por defecto para este tipo');
            $table->boolean('activo')->default(true)->comment('Si el tipo está activo');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('codigo');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_usuario');
    }
};
