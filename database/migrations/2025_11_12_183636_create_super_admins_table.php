<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('super_admins', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->integer('nivel_acceso')->nullable();

            $table->primary('user_id');
            $table->foreign('user_id')->references('id_usuario')->on('usuarios')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('super_admins');
    }
};
