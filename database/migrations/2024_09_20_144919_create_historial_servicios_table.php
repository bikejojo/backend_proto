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
        Schema::create('service_client_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clientId');  // Traducción de 'cliente_id'
            $table->unsignedBigInteger('technicianId');  // Traducción de 'tecnico_id'
            $table->unsignedBigInteger('jobId');  // Traducción de 'solicitud_id'
            $table->bigInteger('descriptionJob')->nullable();  // Traducción de 'solicitud_id'------> 1 ----------> 2
            $table->bigInteger('stateId')->nullable();
            $table->datetime('outsetDate')->nullable();  // Traducción de 'fecha_realizada'
            $table->datetime('finishDate')->nullable();  // Traducción de 'fecha_acabado'
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('clientId')->references('id')->on('internal_clients')->onDelete('cascade');
            $table->foreign('technicianId')->references('id')->on('technicians')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_client_history');
    }
};
