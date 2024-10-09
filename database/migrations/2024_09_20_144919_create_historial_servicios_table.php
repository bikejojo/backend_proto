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
        Schema::create('service_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientId');  // Traducción de 'cliente_id'
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnico_id'
            $table->unsignedBigInteger('requestId');  // Traducción de 'solicitud_id'
            $table->unsignedBigInteger('technicianScheduleId');  // Traducción de 'agenda_tecnico_id'
            $table->datetime('performedDate');  // Traducción de 'fecha_realizada'
            $table->datetime('finishedDate');  // Traducción de 'fecha_acabado'
            $table->unsignedBigInteger('description');  // Traducción de 'descripcion'
            $table->text('duration');  // Traducción de 'duracion'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_history');
    }
};
