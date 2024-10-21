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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();  // Traducción de 'nombre'
            $table->date('certificationDate')->nullable();  // Traducción de 'fecha_certificacion'
            $table->string('photoUrl')->nullable();  // Traducción de 'foto_url'
            $table->unsignedBigInteger('technicianId')->nullable();  // Traducción de 'tecnico_id'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
