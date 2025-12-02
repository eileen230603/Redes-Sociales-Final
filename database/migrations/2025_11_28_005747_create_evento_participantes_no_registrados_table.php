<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('evento_participantes_no_registrados', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')
                ->references('id')
                ->on('eventos')
                ->cascadeOnDelete();
            
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            
            $table->string('estado', 50)->default('pendiente'); // pendiente, aprobada, rechazada
            $table->boolean('asistio')->default(false);
            
            $table->timestamps();
            
            $table->index('evento_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('evento_participantes_no_registrados');
    }
};
