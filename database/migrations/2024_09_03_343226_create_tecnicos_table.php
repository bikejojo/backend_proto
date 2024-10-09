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
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');  // Traducción de 'nombre'
            $table->string('lastName');   // Traducción de 'apellido'
            $table->string('frontIdCard')->nullable();  // Traducción de 'carnet_anverso'
            $table->string('backIdCard')->nullable();   // Traducción de 'carnet_reverso'
            $table->string('email')->nullable();
            $table->string('phoneNumber');  // Traducción de 'telefono'
            $table->string('password');     // Traducción de 'contrasenia'
            $table->string('photo')->nullable();  // Traducción de 'foto'
            $table->unsignedBigInteger('userId');  // Traducción de 'users_id'
            $table->unsignedBigInteger('cityId');  // Traducción de 'ciudades_id'
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cityId')->references('id')->on('cities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};
