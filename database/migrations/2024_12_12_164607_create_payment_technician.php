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
        Schema::table('payment_technician', function (Blueprint $table) {
            //
            $table->id();
            $table->unsignedBigInteger('technicianId');
            $table->unsignedBigInteger('paymentId');
            $table->unsignedBigInteger('subscriptionsId');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_technician', function (Blueprint $table) {
            //
        });
    }
};
