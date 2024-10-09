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
            $table->datetime('registrationDateTime');  // Traducción de 'fecha_tiempo_registrado'
            $table->datetime('expirationDateTime');  // Traducción de 'fecha_tiempo_vencimiento'
            $table->datetime('updatedDateTime')->nullable();  // Traducción de 'fecha_tiempo_actualizado'
            $table->text('requestDescription');  // Traducción de 'descripcion_solicitud'
            $table->decimal('latitude', 10, 7);  // Traducción de 'latitud'
            $table->decimal('longitude', 10, 7);  // Traducción de 'longitud'
            $table->text('locationDescription');  // Traducción de 'descripcion_ubicacion'
            $table->unsignedBigInteger('stateId');  // Traducción de 'estado_id'
            $table->unsignedBigInteger('clientId');  // Traducción de 'cliente_id'
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnico_id'
            $table->foreign('technicianId')->references('id')->on('technicians')->onDelete('cascade');
            $table->foreign('clientId')->references('id')->on('external_clients')->onDelete('cascade');
            $table->foreign('stateId')->references('id')->on('state_types')->onDelete('cascade');
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
