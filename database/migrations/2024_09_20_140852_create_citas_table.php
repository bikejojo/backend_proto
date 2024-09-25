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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->text('descripcion_solicitud')->nullable();
            $table->decimal('latitud',10,7);
            $table->decimal('longitud',10,7);
            $table->text('descripcion_ubicacion');
            $table->text('resultado')->nullable();
            $table->datetime('fecha_hora_registrada');
            $table->datetime('fecha_hora_fin');
            $table->text('duracion')->nullable();
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('solicitud_id');
            $table->foreign('solicitud_id')->references('id')->on('solicituds')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('tipo_estados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
