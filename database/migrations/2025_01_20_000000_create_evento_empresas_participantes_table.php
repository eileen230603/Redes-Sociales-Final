<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evento_empresas_participantes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')
                ->references('id')
                ->on('eventos')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id')
                ->references('user_id')
                ->on('empresas')
                ->cascadeOnDelete();

            $table->string('estado', 50)->default('asignada'); // asignada, confirmada, cancelada
            $table->boolean('asistio')->default(false);
            $table->text('tipo_colaboracion')->nullable(); // Recursos, Logística, Financiera, etc.
            $table->text('descripcion_colaboracion')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Evitar duplicados
            $table->unique(['evento_id', 'empresa_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('evento_empresas_participantes');
    }
};

