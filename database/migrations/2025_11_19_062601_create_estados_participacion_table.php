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
        Schema::create('estados_participacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único del estado (ej: pendiente, aprobada)');
            $table->string('nombre', 100)->comment('Nombre del estado');
            $table->text('descripcion')->nullable()->comment('Descripción del estado');
            $table->string('color', 20)->default('secondary')->comment('Color del badge');
            $table->string('icono', 50)->nullable()->comment('Clase de icono FontAwesome');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            $table->boolean('activo')->default(true)->comment('Si el estado está activo');
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
        Schema::dropIfExists('estados_participacion');
    }
};
