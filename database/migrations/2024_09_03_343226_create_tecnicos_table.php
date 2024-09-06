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
        Schema::create('tecnicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('carnet_anverso');
            $table->string('carnet_reverso');
            $table->string('correo');
            $table->string('telefono');
            $table->string('contrasenia');
            $table->string('foto');
            $table->unsignedBigInteger("users_id");
            $table->unsignedBigInteger("ciudades_id");
            $table->foreign("users_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("ciudades_id")->references("id")->on("ciudades")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnicos');
    }
};
