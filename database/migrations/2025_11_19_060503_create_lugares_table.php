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
        Schema::create('lugares', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200)->comment('Nombre del lugar (ej: Parque Central, Auditorio Municipal)');
            $table->text('direccion')->nullable()->comment('Dirección completa');
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades')->onDelete('set null');
            $table->decimal('lat', 10, 7)->nullable()->comment('Latitud');
            $table->decimal('lng', 10, 7)->nullable()->comment('Longitud');
            $table->integer('capacidad')->nullable()->comment('Capacidad máxima del lugar');
            $table->text('descripcion')->nullable()->comment('Descripción del lugar');
            $table->string('telefono', 20)->nullable()->comment('Teléfono de contacto');
            $table->string('email', 100)->nullable()->comment('Email de contacto');
            $table->boolean('activo')->default(true)->comment('Si el lugar está activo');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('ciudad_id');
            $table->index('nombre');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lugares');
    }
};
