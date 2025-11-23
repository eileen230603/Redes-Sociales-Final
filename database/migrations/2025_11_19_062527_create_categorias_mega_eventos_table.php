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
        Schema::create('categorias_mega_eventos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único de la categoría (ej: social, cultural)');
            $table->string('nombre', 100)->comment('Nombre de la categoría');
            $table->text('descripcion')->nullable()->comment('Descripción de la categoría');
            $table->string('icono', 50)->nullable()->comment('Clase de icono FontAwesome');
            $table->string('color', 20)->default('primary')->comment('Color del badge');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            $table->boolean('activo')->default(true)->comment('Si la categoría está activa');
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
        Schema::dropIfExists('categorias_mega_eventos');
    }
};
