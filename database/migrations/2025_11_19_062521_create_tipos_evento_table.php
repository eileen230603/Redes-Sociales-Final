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
        Schema::create('tipos_evento', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único del tipo (ej: conferencia, taller)');
            $table->string('nombre', 100)->comment('Nombre del tipo de evento');
            $table->text('descripcion')->nullable()->comment('Descripción del tipo de evento');
            $table->string('icono', 50)->nullable()->comment('Clase de icono FontAwesome');
            $table->string('color', 20)->default('primary')->comment('Color del badge (primary, success, info, etc.)');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
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
        Schema::dropIfExists('tipos_evento');
    }
};
