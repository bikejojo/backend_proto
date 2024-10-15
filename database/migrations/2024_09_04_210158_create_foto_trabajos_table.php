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
        Schema::create('work_photos', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();  // Traducción de 'descripcion'
            $table->text('photoUrls')->nullable();  // Traducción de 'fotos_url'
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnicos_id'
            $table->foreign('technicianId')->references('id')->on('technicians')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_photos');
    }
};
