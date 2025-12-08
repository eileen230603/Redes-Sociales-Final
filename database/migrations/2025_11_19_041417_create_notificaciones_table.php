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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ong_id'); // user_id de la ONG
            $table->unsignedBigInteger('evento_id')->nullable();
            $table->unsignedBigInteger('externo_id')->nullable(); // user_id del usuario que reaccionó
            $table->string('tipo', 50); // 'reaccion', 'participacion', etc.
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->boolean('leida')->default(false);
            $table->timestamps();

            // Índices y constraints
            $table->foreign('ong_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('cascade');
            $table->foreign('externo_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->index('ong_id');
            $table->index('leida');
            $table->index(['ong_id', 'leida']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
