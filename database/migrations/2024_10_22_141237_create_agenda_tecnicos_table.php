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
        Schema::create('technician_agenda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnico_id'
            $table->datetime('createDate');
            $table->foreign('technicianId')->references('id')->on('technicians');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_schedules');
    }
};
