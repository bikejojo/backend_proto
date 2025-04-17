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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('action_key');
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->enum('type', ['solicitud', 'servicio' , 'aceptacion', 'rechazo', 'promocion', 'publicidad' , 'mensaje']);
            $table->timestamp('send_at')->nullable();
            $table->enum('status', ['pendiente', 'enviada', 'fallida', 'cancelada'])->default('pendiente');
            $table->string('type_users')->nullable();
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade'); // ID del usuario que envía la notificación en otras palabras quien genero
            $table->index('sender_id');
            $table->index('action_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
