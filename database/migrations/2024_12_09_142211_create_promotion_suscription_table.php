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
        Schema::create('promotion_suscription', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subcriptionsId');
            $table->unsignedBigInteger('promotionId');

            $table->foreign('subcriptionsId')->references('id')->on('subcriptions');
            $table->foreign('promotionId')->references('id')->on('promotion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_suscription');
    }
};
