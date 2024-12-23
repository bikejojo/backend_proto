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
            $table->unsignedBigInteger('referenceId');
            $table->unsignedBigInteger('stateId');
            $table->unsignedBigInteger('clientId');
            $table->unsignedBigInteger('typeClient');
            $table->string('type');
            $table->string('descriptionState')->nullable();
            $table->string('observations')->nullable();
            $table->datetime('dateCreate')->nullable();
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
