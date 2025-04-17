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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID del usuario
            $table->foreignId('device_id')->constrained('devices'); // ID del dispositivo
            $table->string('expo_token');      // Expo Push Token
            $table->boolean('is_active')->default(true);
            //$table->timestamp('dateCreate')->nullable();
            $table->index('user_id');
            $table->unique('expo_token'); // muy recomendable
            $table->index('is_active');
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
