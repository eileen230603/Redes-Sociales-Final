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
        Schema::create('evento_reacciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evento_id');
            $table->unsignedBigInteger('externo_id'); // user_id del usuario externo
            $table->timestamps();

            // Índices y constraints
            $table->unique(['evento_id', 'externo_id'], 'unique_evento_externo');
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('cascade');
            $table->foreign('externo_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->index('evento_id');
            $table->index('externo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_reacciones');
    }
};
