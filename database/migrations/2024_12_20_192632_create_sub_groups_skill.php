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
        Schema::create('sub_groups_skill', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skillId')->nullable();
            $table->unsignedBigInteger('subGroupId')->nullable();
            $table->datetime('createDate');
            $table->foreign('skillId')->references('id')->on('skills');
            $table->foreign('subGroupId')->references('id')->on('sub_groups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_groups_skill');
    }
};
