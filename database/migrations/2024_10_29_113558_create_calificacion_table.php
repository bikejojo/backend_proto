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
        Schema::create('rating', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rating');
            $table->text('feedback');
            $table->unsignedBigInteger('serviceId');
            $table->unsignedBigInteger('technicialId');
            $table->unsignedBigInteger('clientId');
            $table->foreign('clientId')->references('id')->on('internal_clients');
            $table->foreign('serviceId')->references('id')->on('services');
            $table->foreign('technicialId')->references('id')->on('technicians');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating');
    }
};
