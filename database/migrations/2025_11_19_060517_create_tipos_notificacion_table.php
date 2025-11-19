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
        Schema::create('tipos_notificacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único del tipo (ej: reaccion_evento, nueva_participacion)');
            $table->string('nombre', 100)->comment('Nombre del tipo de notificación');
            $table->text('descripcion')->nullable()->comment('Descripción del tipo');
            $table->text('plantilla_mensaje')->nullable()->comment('Plantilla del mensaje (puede usar variables)');
            $table->string('icono', 50)->nullable()->comment('Icono FontAwesome');
            $table->string('color', 20)->default('info')->comment('Color del badge');
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
        Schema::dropIfExists('tipos_notificacion');
    }
};
