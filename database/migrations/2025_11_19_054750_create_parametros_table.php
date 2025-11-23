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
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 100)->unique()->comment('Código único del parámetro (ej: max_eventos_por_ong)');
            $table->string('nombre', 200)->comment('Nombre descriptivo del parámetro');
            $table->text('descripcion')->nullable()->comment('Descripción detallada del parámetro');
            $table->string('categoria', 50)->default('general')->comment('Categoría: general, eventos, usuarios, notificaciones, etc.');
            $table->string('tipo', 20)->default('texto')->comment('Tipo: texto, numero, booleano, json, fecha');
            $table->text('valor')->nullable()->comment('Valor del parámetro');
            $table->text('valor_defecto')->nullable()->comment('Valor por defecto');
            $table->json('opciones')->nullable()->comment('Opciones disponibles (para select, radio, etc.)');
            $table->string('grupo', 50)->nullable()->comment('Grupo al que pertenece (para agrupar en la UI)');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            $table->boolean('editable')->default(true)->comment('Si el parámetro puede ser editado');
            $table->boolean('visible')->default(true)->comment('Si el parámetro es visible en la UI');
            $table->boolean('requerido')->default(false)->comment('Si el parámetro es requerido');
            $table->string('validacion', 500)->nullable()->comment('Reglas de validación adicionales');
            $table->text('ayuda')->nullable()->comment('Texto de ayuda para el usuario');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('codigo');
            $table->index('categoria');
            $table->index('grupo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};
