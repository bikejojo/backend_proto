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
            $table->text('account');
            $table->BigInteger('status');
            $table->text('description');
            $table->datetime('createDate')->nullable();
            $table->datetime('finishDate')->nullable();
            $table->unsignedBigInteger('technicianId');
            $table->foreign('technicianId')->references('id')->on('technicians');
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
