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
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->comment('Nombre de la ciudad');
            $table->string('codigo_postal', 20)->nullable()->comment('Código postal');
            $table->string('departamento', 100)->nullable()->comment('Departamento o provincia');
            $table->string('pais', 100)->default('Bolivia')->comment('País');
            $table->decimal('lat', 10, 7)->nullable()->comment('Latitud');
            $table->decimal('lng', 10, 7)->nullable()->comment('Longitud');
            $table->boolean('activo')->default(true)->comment('Si la ciudad está activa');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('nombre');
            $table->index('departamento');
            $table->index('pais');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};
