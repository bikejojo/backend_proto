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
            $table->unsignedInteger('stateId');
            $table->unsignedInteger('requestsId')->nullable();
            $table->unsignedInteger('technicalId');
            $table->unsignedInteger('clientId');
            $table->string('typeClient');
            $table->string('serviceDescription');
            $table->string('status');
            $table->datetime('programDate')->nullable();
            $table->datetime('requestsDate')->nullable();
            $table->datetime('finishedDate')->nullable();
            $table->foreign('stateId')->references('id')->on('state_types')->onDelete('cascade');
            $table->foreign('requestsId')->references('id')->on('requests')->onDelete('cascade');
            #$table->foreign('technicalId')->references('id')->on('technicians')->onDelete('cascade');
            #$table->foreign('clientId')->references('id')->on('internal_clients')->onDelete('cascade');
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
