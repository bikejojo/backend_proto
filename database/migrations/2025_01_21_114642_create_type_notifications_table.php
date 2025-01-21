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
        Schema::create('type_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notifications_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->bigInteger('read')->nullable();
            $table->text('image')->nullable();
            $table->string('status')->nullable();
            $table->datetime('read_at')->nullable();
            $table->datetime('date_time_at')->nullable();
            $table->index('type_id'); // Consultas por tipo de notificación
            $table->index('notifications_id'); // Relación con notifications
            $table->foreign('type_id')->references('id')->on('type');
            $table->foreign('notifications_id')->references('id')->on('notifications');
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
