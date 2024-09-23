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
            $table->text('descripcion_solicitud');
            $table->text('ubicacion');
            $table->text('resultado');
            $table->datetime('fecha_hora_registrada');
            $table->datetime('fecha_hora_fin');
            $table->text('duracion');
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
