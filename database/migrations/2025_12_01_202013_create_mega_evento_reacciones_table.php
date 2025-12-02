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
        Schema::create('mega_evento_reacciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mega_evento_id');
            $table->unsignedBigInteger('externo_id')->nullable(); // user_id del usuario externo (nullable para usuarios no registrados)
            $table->string('nombres', 100)->nullable(); // Para usuarios no registrados
            $table->string('apellidos', 100)->nullable(); // Para usuarios no registrados
            $table->string('email', 255)->nullable(); // Para usuarios no registrados
            $table->timestamps();

            // Índices y constraints
            $table->unique(['mega_evento_id', 'externo_id', 'email'], 'unique_mega_evento_externo_email');
            $table->foreign('mega_evento_id')->references('mega_evento_id')->on('mega_eventos')->onDelete('cascade');
            $table->foreign('externo_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->index('mega_evento_id');
            $table->index('externo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mega_evento_reacciones');
    }
};
