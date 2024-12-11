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
        Schema::create('promotion', function (Blueprint $table) {
            $table->id();
            $table->text('codePromotion');
            $table->string('namePromotion');
            $table->string('description')->nullable();
            $table->string('type')->nullable();
            $table->BigInteger('discount_value')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('finishDate')->nullabe();
            $table->BigInteger('duration')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion');
    }
};
