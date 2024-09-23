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
        Schema::create('agenda_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tecnico_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('note_id')->nullable();
            $table->unsignedBigInteger('cita_id');
            $table->datetime('fecha_creada');
            $table->datetime('fecha_proxima');
            $table->text('descripcion_proxima');
            $table->unsignedBigInteger('tipo_actividad_id');
            $table->foreign('cliente_id')->references('id')->on('cliente_externos')->onDelete('cascade');
            $table->foreign('tecnico_id')->references('id')->on('tecnicos')->onDelete('cascade');
            $table->foreign('note_id')->references('id')->on('notes')->onDelete('cascade');
            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('tipo_actividad_id')->references('id')->on('tipo_actividades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_tecnicos');
    }
};
