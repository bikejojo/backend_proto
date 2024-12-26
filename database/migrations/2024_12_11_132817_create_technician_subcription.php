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
        Schema::create('technician_subcription', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('technicianId');
            $table->unsignedBigInteger('subcriptionsId')->nullable();
            $table->datetime('starDateSubcription')->nullable();
            $table->datetime('endDateSubcription')->nullable();
            $table->bigInteger('status')->nullable();
            $table->foreign('technicianId')->references('id')->on('technicians');
            $table->foreign('subcriptionsId')->references('id')->on('subcriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_subcription');
    }
};
