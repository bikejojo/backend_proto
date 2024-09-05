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
        Schema::create('tecnico_habilidades', function (Blueprint $table) {
            $table->id();
            $table->string('experiencia');
            $table->unsignedBigInteger('tecnico_id');
            $table->unsignedBigInteger('habilidades_id');
            $table->foreign("habilidades_id")->references("id")->on("habilidades")->onDelete("cascade");
            $table->foreign("tecnico_id")->references("id")->on("tecnicos")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnico_habilidades');
    }
};
