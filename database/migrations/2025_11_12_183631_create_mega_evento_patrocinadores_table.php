<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('mega_evento_patrocinadores', function (Blueprint $table) {
            $table->unsignedBigInteger('mega_evento_id');
            $table->unsignedBigInteger('empresa_id'); // empresas.user_id
            $table->string('tipo_patrocinio', 100)->nullable();
            $table->decimal('monto_contribucion', 15, 2)->nullable();
            $table->string('tipo_contribucion', 100)->nullable();
            $table->text('descripcion_contribucion')->nullable();
            $table->dateTime('fecha_compromiso')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('estado_compromiso', 50)->default('confirmado');
            $table->boolean('activo')->default(true);

            $table->primary(['mega_evento_id','empresa_id']);
            $table->foreign('mega_evento_id')->references('mega_evento_id')->on('mega_eventos')->cascadeOnDelete();
            $table->foreign('empresa_id')->references('user_id')->on('empresas')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mega_evento_patrocinadores');
    }
};
