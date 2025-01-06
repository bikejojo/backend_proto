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
            $table->text('full_name')->nullable();
            $table->text('phone_number')->nullable();
            $table->bigInteger('updated_by_technician')->nullable();
            $table->bigInteger('version')->nullable();
            $table->text('status')->nullable();//1 activo y 0 eliminado
            $table->foreign('clientId')->references('id')->on('external_clients');
            $table->foreign('technicalId')->references('id')->on('technicians');
            $table->timestamps();
        });
        /*  $table->text('full_name')->nullable();
            $table->text('phone_number')->nullable();
            $table->text('updated_by_technician')->nullable();
            $table->bigInteger('version')->nullable();
            $table->unsignedBigInteger('clientId_');
            $table->unsignedBigInteger('technicalId');
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associationTechnClient');
    }
};
