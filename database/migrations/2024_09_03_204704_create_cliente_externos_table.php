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
        Schema::create('external_clients', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');  // Traducción de 'nombre'
            $table->string('lastName');   // Traducción de 'apellido'
            $table->string('email');
            $table->string('loginMethod')->nullable();  // Traducción de 'metodo_login'
            $table->string('photo')->nullable();
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
        Schema::dropIfExists('external_clients');
    }
};
