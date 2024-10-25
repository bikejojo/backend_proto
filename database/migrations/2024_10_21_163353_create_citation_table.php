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
        Schema::create('citations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('technicialId');
            $table->unsignedBigInteger('clientId');
            $table->unsignedBigInteger('serviceId')->nullable();
            $table->unsignedBigInteger('activityId');
            $table->unsignedBigInteger('typeClient');
            $table->text('citationDescription');
            $table->dateTime('cratedDate');
            $table->dateTime('nextDate')->nullable();
            $table->datetime('finishedDate')->nullable();
            $table->foreign('serviceId')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('activityId')->references('id')->on('activity_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citations');
    }
};
