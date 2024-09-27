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
        Schema::create('solicituds', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha_tiempo_registrado');
            $table->datetime('fecha_tiempo_vencimiento');
            $table->datetime('fecha_tiempo_actualizado')->nullable();
            $table->text('descripcion_servicio');
            $table->decimal('latitud',10,7);
            $table->decimal('longitud',10,7);
            $table->text('descripcion_ubicacion');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('tecnico_id');
            $table->foreign('tecnico_id')->references('id')->on('tecnicos')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('cliente_externos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicituds');
    }
};
