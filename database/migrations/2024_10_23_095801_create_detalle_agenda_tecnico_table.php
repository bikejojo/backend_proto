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
        Schema::create('detail_technical_agenda', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientId');
            $table->unsignedBigInteger('agendaTechnicalId');
            $table->unsignedBigInteger('serviceId')->nullable();
            $table->unsignedInteger('typeClient');
            $table->datetime('createDate');
            $table->datetime('serviceDate')->nullable();
            $table->foreign('agendaTechnicalId')->references('id')->on('technician_agenda');
            $table->foreign('serviceId')->references('id')->on('services');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_technical_agenda');
    }
};
