<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('mega_eventos', function (Blueprint $table) {
            $table->id('mega_evento_id');

            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();

            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');

            $table->string('ubicacion', 500)->nullable();

            $table->dateTime('fecha_creacion')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('fecha_actualizacion')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->boolean('activo')->default(true);

            $table->string('categoria', 50)->default('social');

            $table->unsignedBigInteger('ong_organizadora_principal')->nullable();
            $table->foreign('ong_organizadora_principal')
                ->references('user_id')
                ->on('ongs')
                ->cascadeOnDelete();

            $table->integer('capacidad_maxima')->nullable();
            $table->boolean('es_publico')->default(false);
            $table->string('estado', 20)->default('planificacion');
        });
    }

    public function down(): void {
        Schema::dropIfExists('mega_eventos');
    }
};
