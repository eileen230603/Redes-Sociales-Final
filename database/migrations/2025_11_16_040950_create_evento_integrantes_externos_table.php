<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evento_integrantes_externos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')
                ->references('id')
                ->on('eventos')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('integrante_externo_id');
            $table->foreign('integrante_externo_id')
                ->references('user_id')
                ->on('integrantes_externos')
                ->cascadeOnDelete();

            $table->string('rol', 100)->nullable();
            $table->boolean('confirmado')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('evento_integrantes_externos');
    }
};
