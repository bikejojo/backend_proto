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
        Schema::create('technician_skills', function (Blueprint $table) {
            $table->id();
            $table->string('experience')->nullable();  // Traducción de 'experiencia'
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnico_id'
            $table->unsignedBigInteger('skillId');  // Traducción de 'habilidad_id'
            $table->foreign('skillId')->references('id')->on('skills');
            $table->foreign('technicianId')->references('id')->on('technicians');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_skills');
    }
};
