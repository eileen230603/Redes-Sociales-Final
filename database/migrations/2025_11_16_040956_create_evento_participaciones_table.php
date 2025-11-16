<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evento_participaciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')
                ->references('id')
                ->on('eventos')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('externo_id');
            $table->foreign('externo_id')
                ->references('id_usuario')
                ->on('usuarios')
                ->cascadeOnDelete();

            $table->boolean('asistio')->default(false);
            $table->integer('puntos')->default(0);

            $table->timestamps();

            $table->unique(['evento_id', 'externo_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('evento_participaciones');
    }
};
