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
        Schema::create('detalle_agenda_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_proxima');
            $table->text('descripcion_proxima');
            $table->unsignedBigInteger('agenda_tecnico_id');
            $table->unsignedBigInteger('tipo_actividad_id');
            $table->foreign('agenda_tecnico_id')->references('id')->on('agenda_tecnicos')->onDelete('cascade');
            $table->foreign('tipo_actividad_id')->references('id')->on('tipo_actividades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_agenda_tecnicos');
    }
};
