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
        Schema::create('contact', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientInternalId');
            $table->unsignedBigInteger('technicalId');
            $table->unsignedBigInteger('statusId');
            $table->date('dateRegistered');
            $table->text('issue');
            $table->foreign('clientInternalId')->references('id')->on('clientInternal')->onDelete('cascade');
            $table->foreign('technicalId')->references('id')->on('technicians')->onDelete('cascade');
            $table->foreign('statusId')->references('id')->on('typeStatus')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact');
    }
};
