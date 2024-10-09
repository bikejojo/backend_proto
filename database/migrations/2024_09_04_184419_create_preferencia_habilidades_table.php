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
        Schema::create('skill_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skillId');  // Traducción de 'habilidades_id'
            $table->unsignedBigInteger('clientId');  // Traducción de 'cliente_id'
            $table->foreign('skillId')->references('id')->on('skills')->onDelete('cascade');
            $table->foreign('clientId')->references('id')->on('external_clients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_preferences');
    }
};
