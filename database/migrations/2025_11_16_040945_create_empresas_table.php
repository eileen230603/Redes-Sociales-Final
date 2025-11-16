<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('empresas', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();

            $table->string('nombre_empresa', 100);
            $table->string('NIT', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 150)->nullable();
            $table->string('sitio_web', 150)->nullable();
            $table->text('descripcion')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id_usuario')
                ->on('usuarios')
                ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('empresas');
    }
};
