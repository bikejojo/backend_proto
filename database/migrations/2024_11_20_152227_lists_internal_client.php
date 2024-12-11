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
        //
        schema::create('list_internal_clients',function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('technicianId')->nullable();
            $table->unsignedBigInteger('clientId')->nullable();
            $table->unsignedBigInteger('typeClient')->nullable();
            $table->unsignedBigInteger('requestsId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
