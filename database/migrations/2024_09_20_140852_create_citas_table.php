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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->text('appointmentDescription')->nullable();  // Traducción de 'descripcion_cita'
            $table->decimal('latitude', 10, 7);  // Traducción de 'latitud'
            $table->decimal('longitude', 10, 7);  // Traducción de 'longitud'
            $table->text('locationDescription');  // Traducción de 'descripcion_ubicacion'
            $table->text('result')->nullable();  // Traducción de 'resultado'
            $table->datetime('registrationDateTime');  // Traducción de 'fecha_hora_registrada'
            $table->datetime('endDateTime');  // Traducción de 'fecha_hora_fin'
            $table->unsignedBigInteger('stateId');  // Traducción de 'estado_id'
            $table->unsignedBigInteger('requestId');  // Traducción de 'solicitud_id'
            $table->foreign('requestId')->references('id')->on('requests')->onDelete('cascade');
            $table->foreign('stateId')->references('id')->on('state_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
