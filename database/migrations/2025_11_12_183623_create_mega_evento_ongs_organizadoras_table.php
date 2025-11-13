<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('mega_evento_ongs_organizadoras', function (Blueprint $table) {
            $table->unsignedBigInteger('mega_evento_id');
            $table->unsignedBigInteger('ong_id'); // ongs.user_id
            $table->string('rol_organizacion', 100)->nullable();
            $table->dateTime('fecha_union')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('activo')->default(true);

            $table->primary(['mega_evento_id','ong_id']);
            $table->foreign('mega_evento_id')->references('mega_evento_id')->on('mega_eventos')->cascadeOnDelete();
            $table->foreign('ong_id')->references('user_id')->on('ongs')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mega_evento_ongs_organizadoras');
    }
};
