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
        // Primero eliminar la foreign key y el índice único
        Schema::table('evento_reacciones', function (Blueprint $table) {
            $table->dropForeign(['externo_id']);
            $table->dropUnique('unique_evento_externo');
        });
        
        // Luego hacer externo_id nullable y agregar campos nuevos
        Schema::table('evento_reacciones', function (Blueprint $table) {
            // Hacer externo_id nullable para permitir usuarios no registrados
            $table->unsignedBigInteger('externo_id')->nullable()->change();
            
            // Agregar campos para usuarios no registrados
            $table->string('nombres', 100)->nullable()->after('externo_id');
            $table->string('apellidos', 100)->nullable()->after('nombres');
            $table->string('email', 255)->nullable()->after('apellidos');
        });
        
        // Recrear la foreign key solo para valores no null (opcional, pero mejor mantenerla)
        // Nota: Laravel no soporta foreign keys condicionales directamente, así que la omitimos
        // En su lugar, manejaremos la integridad referencial en la aplicación
        
        // Crear nuevo índice único que permita múltiples nulls
        Schema::table('evento_reacciones', function (Blueprint $table) {
            // Índice compuesto que permite múltiples nulls en externo_id
            // Para usuarios registrados: único por evento_id + externo_id
            // Para usuarios no registrados: único por evento_id + email (si email existe)
            $table->unique(['evento_id', 'externo_id', 'email'], 'unique_evento_externo_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evento_reacciones', function (Blueprint $table) {
            // Eliminar el nuevo índice
            $table->dropUnique('unique_evento_externo_email');
        });
        
        Schema::table('evento_reacciones', function (Blueprint $table) {
            // Eliminar campos de no registrado
            $table->dropColumn(['nombres', 'apellidos', 'email']);
            
            // Hacer externo_id no nullable nuevamente
            $table->unsignedBigInteger('externo_id')->nullable(false)->change();
        });
        
        // Restaurar el índice original y la foreign key
        Schema::table('evento_reacciones', function (Blueprint $table) {
            $table->unique(['evento_id', 'externo_id'], 'unique_evento_externo');
        });
        
        // Recrear la foreign key solo si no existe
        if (!Schema::hasColumn('evento_reacciones', 'externo_id')) {
            Schema::table('evento_reacciones', function (Blueprint $table) {
                $table->foreign('externo_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            });
        }
    }
};
