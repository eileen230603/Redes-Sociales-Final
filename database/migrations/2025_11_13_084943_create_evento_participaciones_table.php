<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evento_participaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evento_id')
                ->constrained('eventos')
                ->onDelete('cascade');

            $table->foreignId('externo_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->boolean('asistio')->default(false);
            $table->integer('puntos')->default(0);

            $table->timestamps();

            $table->unique(['evento_id', 'externo_id']); // Un externo solo se inscribe una vez
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_participaciones');
    }
};
