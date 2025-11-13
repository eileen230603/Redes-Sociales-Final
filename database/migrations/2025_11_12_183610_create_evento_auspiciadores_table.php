<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evento_auspiciadores', function (Blueprint $table) {
            $table->id();

            // 🔹 Relación con eventos
            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')
                  ->references('id') // ✅ referencia correcta
                  ->on('eventos')
                  ->onDelete('cascade');

            // 🔹 Relación con empresa o entidad auspiciadora
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->foreign('empresa_id')
                  ->references('user_id')
                  ->on('empresas')
                  ->onDelete('cascade');

            // 🔹 Información adicional
            $table->string('tipo_aporte', 100)->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_auspiciadores');
    }
};
