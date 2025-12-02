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
        Schema::create('mega_evento_compartidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mega_evento_id');
            $table->unsignedBigInteger('externo_id')->nullable(); // Usuario registrado que compartió
            $table->string('nombres', 100)->nullable(); // Para usuarios no registrados
            $table->string('apellidos', 100)->nullable(); // Para usuarios no registrados
            $table->string('email', 255)->nullable(); // Para usuarios no registrados
            $table->string('metodo', 50)->default('link'); // link, qr, whatsapp, facebook, etc.
            $table->timestamps();

            // Índices y constraints
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
        Schema::dropIfExists('mega_evento_compartidos');
    }
};
