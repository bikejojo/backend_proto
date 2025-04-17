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
        Schema::create('notifications_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications');
            $table->string('type_users')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID del usuario a quien se envio
            $table->string('expo_response')->nullable();
            $table->boolean('is_read')->default(false);
            $table->index('user_id');
            $table->index('notification_id');
            $table->index('is_read');
            $table->index(['user_id', 'is_read']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications_user');
    }
};
