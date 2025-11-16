<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('integrantes_externos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();

            $table->string('nombres', 100);
            $table->string('apellidos', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->text('descripcion')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id_usuario')
                ->on('usuarios')
                ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('integrantes_externos');
    }
};
