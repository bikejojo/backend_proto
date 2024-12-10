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
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->text('bank');
            $table->text('account');
            $table->text('social_reason');
            $table->decimal('amount',10,2)->nullable();
            $table->decimal('amount_promotion',10,2)->nullable();
            $table->decimal('amount_pay',10,2)->nullable();
            $table->text('method_payment');
            $table->date('date_payment')->nullable();
            $table->text('photo_qr')->nullable();
            $table->BigInteger('status');
            $table->unsignedBigInteger('subscriptionId');
            $table->foreign('subscriptionId')->references('id')->on('subcriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
