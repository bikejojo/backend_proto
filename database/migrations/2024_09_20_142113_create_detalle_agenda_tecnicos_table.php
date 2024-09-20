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
            $table->unsignedBigInteger('tipo_actividad_id');
            $table->text('duracion');
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
