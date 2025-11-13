<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evento_integrantes_externos', function (Blueprint $table) {
            $table->id();

            // 🔹 Relación correcta con eventos
            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')
                  ->references('id') // ✅ apunta a eventos.id
                  ->on('eventos')
                  ->onDelete('cascade');

            // 🔹 Relación con integrante externo
            $table->unsignedBigInteger('integrante_externo_id');
            $table->foreign('integrante_externo_id')
                  ->references('user_id') // o 'id' según tu tabla integrantes_externos
                  ->on('integrantes_externos')
                  ->onDelete('cascade');

            // 🔹 Rol o función en el evento
            $table->string('rol', 100)->nullable();

            // 🔹 Estado de participación
            $table->boolean('confirmado')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_integrantes_externos');
    }
};
