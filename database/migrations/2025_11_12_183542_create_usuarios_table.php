<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre_usuario', 50)->unique();
            $table->string('correo_electronico', 100)->unique();
            $table->string('contrasena', 255);
            $table->dateTime('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('tipo_usuario', 25);
            $table->boolean('activo')->default(true);
        });

        DB::statement("ALTER TABLE usuarios
            ADD CONSTRAINT tipo_usuario_chk CHECK (tipo_usuario IN ('Super admin','Integrante externo','ONG','Empresa'));");
    }

    public function down(): void {
        Schema::dropIfExists('usuarios');
    }
};
