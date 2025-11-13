<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            // 🔹 Llave primaria
            $table->id();

            // 🔹 Relación con ONG (usa user_id como PK en ongs)
            $table->unsignedBigInteger('ong_id');
            $table->foreign('ong_id')
                  ->references('user_id')
                  ->on('ongs')
                  ->onDelete('cascade');

            // 🔹 Datos principales
            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->string('tipo_evento', 100);

            // 🔹 Fechas (manteniendo snake_case)
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamp('fecha_limite_inscripcion')->nullable();

            // 🔹 Configuración
            $table->integer('capacidad_maxima')->nullable();
            $table->boolean('inscripcion_abierta')->default(true);
            $table->enum('estado', ['borrador', 'publicado', 'cancelado'])->default('borrador');

            // 🔹 Ubicación
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 255)->nullable();

            // 🔹 Recursos e IDs asociados (JSON)
            $table->json('imagenes')->nullable();
            $table->json('patrocinadores')->nullable();
            $table->json('auspiciadores')->nullable();
            $table->json('invitados')->nullable();

            // 🔹 Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
