<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Si la tabla ya existe, no hacer nada (la migración de corrección se encargará de agregar columnas faltantes)
        if (!Schema::hasTable('ong_exportaciones_pdf')) {
        Schema::create('ong_exportaciones_pdf', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ong_id');
            $table->string('tipo', 20)->default('pdf');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('numero_exportacion')->default(1);
            $table->string('folio', 20)->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('ong_id');
            $table->index(['ong_id', 'tipo', 'created_at']);
            $table->unique(['ong_id', 'tipo', 'fecha_inicio', 'fecha_fin', 'numero_exportacion'], 'unique_exportacion_pdf');
            
                // Foreign key - ongs tiene user_id como clave primaria, no id
                $table->foreign('ong_id')->references('user_id')->on('ongs')->onDelete('cascade');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ong_exportaciones_pdf');
    }
};
