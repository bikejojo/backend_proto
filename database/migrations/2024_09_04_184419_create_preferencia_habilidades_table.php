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
        Schema::create('preferencia_habilidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("habilidades_id");
            $table->unsignedBigInteger("cliente_id");
            $table->foreign("habilidades_id")->references("id")->on("habilidades")->onDelete("cascade");;
            $table->foreign("cliente_id")->references("id")->on("cliente_externos")->onDelete("cascade");;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferencia_habilidades');
    }
};
