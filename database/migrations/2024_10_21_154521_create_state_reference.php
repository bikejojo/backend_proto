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
        Schema::create('state_reference', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requestId')->nullable();
            $table->unsignedBigInteger('serviceId')->nullable();
            $table->text('type')->nullable();
            $table->unsignedBigInteger('stateId')->nullable();
            $table->unsignedBigInteger('technicianId')->nullable();
            $table->unsignedBigInteger('clientId')->nullable();
            $table->unsignedBigInteger('typeClient')->nullable();
            $table->string('descriptionState')->nullable();
            $table->string('observations')->nullable();
            $table->datetime('dateCreate')->nullable();
            $table->foreign('requestId')->references('id')->on('requests');
            $table->foreign('serviceId')->references('id')->on('services');
            $table->foreign('stateId')->references('id')->on('state_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_reference');
    }
};
