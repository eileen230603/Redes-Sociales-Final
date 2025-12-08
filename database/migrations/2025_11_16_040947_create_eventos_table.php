<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ong_id');
            $table->foreign('ong_id')
                ->references('user_id')
                ->on('ongs')
                ->cascadeOnDelete();

            $table->string('titulo', 255);
            $table->text('descripcion')->nullable();
            $table->string('tipo_evento', 100);

            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamp('fecha_limite_inscripcion')->nullable();

            $table->integer('capacidad_maxima')->nullable();
            $table->boolean('inscripcion_abierta')->default(true);
            $table->enum('estado', ['borrador', 'publicado', 'finalizado', 'cancelado'])->default('borrador');

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 255)->nullable();

            $table->json('imagenes')->nullable();
            $table->json('patrocinadores')->nullable();
            $table->json('auspiciadores')->nullable();
            $table->json('invitados')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('eventos');
    }
};
