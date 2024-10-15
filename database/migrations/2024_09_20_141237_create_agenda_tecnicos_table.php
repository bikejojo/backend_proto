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
        Schema::create('technician_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnico_id'
            $table->unsignedBigInteger('clientId');  // Traducción de 'cliente_id'
            $table->unsignedBigInteger('noteId')->nullable();  // Traducción de 'note_id'
            $table->unsignedBigInteger('appointmentId');  // Traducción de 'cita_id'
            $table->unsignedBigInteger('activityTypeId');  // Traducción de 'tipo_actividad_id'
            $table->date('createdDate');  // Traducción de 'fecha_creada'
            $table->date('nextDate');  // Traducción de 'fecha_proxima'
            $table->text('nextDescription');  // Traducción de 'descripcion_proxima'
            $table->foreign('clientId')->references('id')->on('internal_clients')->onDelete('cascade');
            $table->foreign('technicianId')->references('id')->on('technicians')->onDelete('cascade');
            $table->foreign('noteId')->references('id')->on('notes')->onDelete('cascade');
            $table->foreign('appointmentId')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('activityTypeId')->references('id')->on('activity_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_schedules');
    }
};
