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
        Schema::create('perfils', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tecnicos_id');
            $table->unsignedBigInteger('tecnico_habilidades_id');
            #$table->foreign('tecnicos_id')->references('id')->on('tecnicos')->onDelete('cascade');
            #$table->foreign('tecnico_habilidades_id')->references('id')->on('tecnico_habilidades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfils');
    }
};
