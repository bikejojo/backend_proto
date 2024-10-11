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
        Schema::create('associationTechnClient', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientId');
            $table->unsignedBigInteger('technicalId');
            $table->datetime('dateTimeCreated');
            $table->foreign('clientId')->references('id')->on('external_clients')->onDelete('cascade');
            $table->foreign('technicalId')->references('id')->on('technicians')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associationTechnClient');
    }
};
