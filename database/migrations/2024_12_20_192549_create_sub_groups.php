<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sub_groups', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->String('description')->nullable();
            $table->datetime('createDate')->nullable();
            $table->bigInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_groups');
    }
};
