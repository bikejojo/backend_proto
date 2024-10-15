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
        Schema::create('subcriptions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('payment_date')->nullable(); // Campo para la fecha de pago
            $table->string('transaction_code')->nullable();
            $table->text('bank');
            $table->text('account');
            $table->decimal('amount', 10, 2);
            $table->boolean('status');
            $table->text('description');
            $table->text('photo_qr');
            $table->unsignedBigInteger('clientsId');
            $table->unsignedBigInteger('technicalId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcriptions');
    }
};
