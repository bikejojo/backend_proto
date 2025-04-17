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
            $table->string('type_users')->nullable();
            $table->string('expo_response')->nullable();
            //$table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('notification_id')->nullable(); // ID de la notificación
            $table->unsignedBigInteger('user_id')->nullable(); // ID del usuario a quien se envio
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade'); // ID de la notificación
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // ID del usuario a quien se envio
            $table->index('user_id');
            $table->index('notification_id');
            //$table->index('is_read');
            $table->index(['user_id', 'notification_id'], 'user_notification_index');
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
