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
        Schema::create('publicity', function (Blueprint $table) {
            $table->id();
            $table->text('descriptionPublicity');
            $table->string('logo')->nullable();
            $table->text('commercialName');
            $table->text('link');
            $table->datetime('createdDate');
            $table->datetime('startDate');
            $table->datetime('finishDate');
            $table->bigInteger('status'); // 1 activo  0 dado de baja  2 expiracion
            $table->unsignedBigInteger('categoryId')->nullable();
            $table->foreign('categoryId')->references('id')->on('category_publicity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicity');
    }
};
