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
        // Verificar si la tabla existe antes de modificarla
        if (Schema::hasTable('ong_exportaciones_pdf')) {
            // PostgreSQL: Usar SQL directo porque Blueprint no soporta "IF NOT EXISTS" para columnas
            if (DB::getDriverName() === 'pgsql') {
                // 1. Agregar columna 'tipo' si no existe (PostgreSQL)
                try {
                    DB::statement("
                        DO $$
                        BEGIN
                            IF NOT EXISTS (
                                SELECT 1 
                                FROM information_schema.columns 
                                WHERE table_schema = 'public' 
                                AND table_name = 'ong_exportaciones_pdf' 
                                AND column_name = 'tipo'
                            ) THEN
                                ALTER TABLE ong_exportaciones_pdf 
                                ADD COLUMN tipo VARCHAR(20) DEFAULT 'pdf';
                            END IF;
                        END $$;
                    ");
                } catch (\Exception $e) {
                    // Ignorar si ya existe
                }
                
                // 2. Agregar columna 'folio' si no existe (PostgreSQL)
                try {
                    DB::statement("
                        DO $$
                        BEGIN
                            IF NOT EXISTS (
                                SELECT 1 
                                FROM information_schema.columns 
                                WHERE table_schema = 'public' 
                                AND table_name = 'ong_exportaciones_pdf' 
                                AND column_name = 'folio'
                            ) THEN
                                ALTER TABLE ong_exportaciones_pdf 
                                ADD COLUMN folio VARCHAR(20) NULL;
                            END IF;
                        END $$;
                    ");
                } catch (\Exception $e) {
                    // Ignorar si ya existe
                }
                
                // 3. Eliminar foreign key existente si existe (PostgreSQL)
                try {
                    $constraints = DB::select("
                        SELECT constraint_name 
                        FROM information_schema.table_constraints 
                        WHERE table_schema = 'public'
                        AND table_name = 'ong_exportaciones_pdf' 
                        AND constraint_type = 'FOREIGN KEY'
                        AND constraint_name LIKE '%ong_id%'
                    ");
                    foreach ($constraints as $constraint) {
                        DB::statement("ALTER TABLE ong_exportaciones_pdf DROP CONSTRAINT IF EXISTS {$constraint->constraint_name}");
                    }
                } catch (\Exception $e) {
                    // Si no existe la constraint, continuar
                }
                
                // 4. Agregar la foreign key correcta que apunta a user_id
                try {
                    DB::statement("
                        ALTER TABLE ong_exportaciones_pdf 
                        ADD CONSTRAINT ong_exportaciones_pdf_ong_id_foreign 
                        FOREIGN KEY (ong_id) 
                        REFERENCES ongs(user_id) 
                        ON DELETE CASCADE
                    ");
                } catch (\Exception $e) {
                    // Ignorar si ya existe
                }
                
                // 5. Crear índices si no existen (PostgreSQL)
                try {
                    $indexExists = DB::select("
                        SELECT 1 
                        FROM pg_indexes 
                        WHERE schemaname = 'public'
                        AND tablename = 'ong_exportaciones_pdf' 
                        AND indexname = 'ong_exportaciones_pdf_ong_id_tipo_created_at_index'
                    ");
                    if (empty($indexExists)) {
                        DB::statement("
                            CREATE INDEX ong_exportaciones_pdf_ong_id_tipo_created_at_index 
                            ON ong_exportaciones_pdf (ong_id, tipo, created_at)
                        ");
                    }
                } catch (\Exception $e) {
                    // Ignorar errores si los índices ya existen
                }
            } else {
                // MySQL/MariaDB
                Schema::table('ong_exportaciones_pdf', function (Blueprint $table) {
                    if (!Schema::hasColumn('ong_exportaciones_pdf', 'tipo')) {
                        $table->string('tipo', 20)->default('pdf')->after('ong_id');
                    }
                    if (!Schema::hasColumn('ong_exportaciones_pdf', 'folio')) {
                        $table->string('folio', 20)->nullable()->after('numero_exportacion');
                    }
                    try {
                        $table->dropForeign(['ong_id']);
                    } catch (\Exception $e) {
                        // Ignorar si no existe
                    }
                    $table->foreign('ong_id')
                        ->references('user_id')
                        ->on('ongs')
                        ->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ong_exportaciones_pdf')) {
            Schema::table('ong_exportaciones_pdf', function (Blueprint $table) {
                try {
                    if (DB::getDriverName() === 'pgsql') {
                        DB::statement('ALTER TABLE ong_exportaciones_pdf DROP CONSTRAINT IF EXISTS ong_exportaciones_pdf_ong_id_foreign');
                    } else {
                        $table->dropForeign(['ong_id']);
                    }
                    
                    // Restaurar la foreign key incorrecta (si es necesario para rollback)
                    $table->foreign('ong_id')
                        ->references('id')
                        ->on('ongs')
                        ->onDelete('cascade');
                } catch (\Exception $e) {
                    // Ignorar errores en rollback
                }
            });
        }
    }
};
