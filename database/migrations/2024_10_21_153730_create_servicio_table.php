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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('stateId')->nullable();
            $table->unsignedInteger('requestsId')->nullable();
            $table->unsignedInteger('technicalId');
            $table->unsignedInteger('clientId');
            $table->unsignedInteger('activityId');
            $table->string('typeClient');
            $table->text('titleService');
            $table->string('serviceDescription');
            $table->text('serviceLocation');
            $table->text('longitude');
            $table->text('latitude');
            $table->string('status');
            $table->datetime('createdDateTime')->nullable();
            $table->datetime('updatedDateTime')->nullable();
            $table->datetime('finishedDateTime')->nullable();
            $table->foreign('stateId')->references('id')->on('state_types')->onDelete('cascade');
            $table->foreign('requestsId')->references('id')->on('requests')->onDelete('cascade');
            $table->foreign('activityId')->references('id')->on('activity_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
