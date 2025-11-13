<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invitados', function (Blueprint $table) {
            $table->id();

            // 🔹 Relación con eventos (corrige aquí)
            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')
                  ->references('id') // ✅ apunta a eventos.id
                  ->on('eventos')
                  ->onDelete('cascade');

            // 🔹 Información del invitado
            $table->string('nombre', 150);
            $table->string('correo', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('cargo', 100)->nullable();

            // 🔹 Estado de participación
            $table->boolean('asistio')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitados');
    }
};
