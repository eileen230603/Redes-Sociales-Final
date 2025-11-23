<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('mega_evento_participantes_externos', function (Blueprint $table) {
            $table->unsignedBigInteger('mega_evento_id');
            $table->unsignedBigInteger('integrante_externo_id');

            $table->string('tipo_participacion', 100)->nullable();
            $table->text('habilidades_ofrecidas')->nullable();
            $table->string('disponibilidad', 500)->nullable();
            $table->string('estado_participacion', 50)->nullable()->default('interesado');

            $table->dateTime('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('comentarios')->nullable();

            $table->boolean('activo')->default(true);

            $table->primary(['mega_evento_id', 'integrante_externo_id']);

            $table->foreign('mega_evento_id')
                ->references('mega_evento_id')
                ->on('mega_eventos')
                ->cascadeOnDelete();

            $table->foreign('integrante_externo_id')
                ->references('user_id')
                ->on('integrantes_externos')
                ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mega_evento_participantes_externos');
    }
};
