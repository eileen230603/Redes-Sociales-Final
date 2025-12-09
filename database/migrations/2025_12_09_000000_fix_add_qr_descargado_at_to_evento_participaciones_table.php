<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si la columna ya existe antes de agregarla
        $columnExists = false;
        try {
            $result = DB::select("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'evento_participaciones' 
                AND column_name = 'qr_descargado_at'
            ");
            $columnExists = count($result) > 0;
        } catch (\Exception $e) {
            // Si hay error, asumir que no existe
            $columnExists = false;
        }

        if (!$columnExists) {
            // Agregar la columna usando SQL directo para PostgreSQL
            DB::statement('ALTER TABLE evento_participaciones ADD COLUMN qr_descargado_at TIMESTAMP NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verificar si la columna existe antes de eliminarla
        $columnExists = false;
        try {
            $result = DB::select("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'evento_participaciones' 
                AND column_name = 'qr_descargado_at'
            ");
            $columnExists = count($result) > 0;
        } catch (\Exception $e) {
            // Si hay error, asumir que no existe
            $columnExists = false;
        }

        if ($columnExists) {
            DB::statement('ALTER TABLE evento_participaciones DROP COLUMN IF EXISTS qr_descargado_at');
        }
    }
};
