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
        Schema::create('notifications_device', function (Blueprint $table) {
            $table->id();
            //$table->morphs('tokenable'); // Relación polimórfica
            $table->string('device_id')->unique(); // ID del dispositivo
            $table->string('token')->unique();      // Expo Push Token
            $table->boolean('is_active')->default(true);
            $table->timestamp('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_notifications');
    }
};
