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
        Schema::create('certificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->date('fecha_certificacion')->nullable();
            $table->string('foto_url')->nullable();
            $table->unsignedBigInteger('tecnico_id')->nullable();
            $table->foreign('tecnico_id')->references('id')->on('tecnicos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certficaciones');
    }
};
