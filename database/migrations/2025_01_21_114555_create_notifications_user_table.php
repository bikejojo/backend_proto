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
            $table->text('token_user')->nullable();
            $table->string('type_device')->nullable();
            $table->unsignedBigInteger('notifications_id')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->unsignedBigInteger('sender_userid')->nullable();
            $table->unsignedBigInteger('receiver_userid')->nullable();
            $table->dateTime('sent_at');
            $table->dateTime('read_at');
            $table->index('receiver_userid');
            $table->index('sender_userid');
            $table->index('read_at'); // Consultas por notificaciones leídas
            $table->index('sent_at');
            $table->foreign('notifications_id')->references('id')->on('notifications');

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
