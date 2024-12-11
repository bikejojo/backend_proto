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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stateId')->nullable();  // Traducción de 'estado_id'
            $table->unsignedBigInteger('clientId');  // Traducción de 'cliente_id'
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnico_id'
            $table->unsignedBigInteger('activityId');
            $table->text('titleRequests')->nullable();
            $table->text('requestDescription');  // Traducción de 'descripcion_solicitud' // Traducción de 'fecha_tiempo_registrado'
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->text('reference_phone');
            $table->datetime('registrationDateTime')->nullable();
            $table->bigInteger('status');
            $table->foreign('technicianId')->references('id')->on('technicians');
            $table->foreign('clientId')->references('id')->on('internal_clients');
            $table->foreign('stateId')->references('id')->on('state_types');
            $table->foreign('activityId')->references('id')->on('activity_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
