<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mega_evento_participantes_no_registrados', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('mega_evento_id');
            $table->foreign('mega_evento_id')
                ->references('mega_evento_id')
                ->on('mega_eventos')
                ->cascadeOnDelete();
            
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            
            $table->string('estado', 50)->default('aprobada'); // aprobada, rechazada (aprobada automáticamente)
            $table->boolean('asistio')->default(false);
            
            $table->timestamps();
            
            $table->index('mega_evento_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('mega_evento_participantes_no_registrados');
    }
};
