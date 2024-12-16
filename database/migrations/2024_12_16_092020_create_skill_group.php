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
        Schema::create('skillsGroups', function (Blueprint $table) {
            //
            $table->id();
            $table->unsignedBigInteger('groupId');
            $table->unsignedBigInteger('skillsId');
            $table->foreign('skillsId')->references('id')->on('skills');
            $table->foreign('groupId')->references('id')->on('group');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categoryGroups', function (Blueprint $table) {
            //
        });
    }
};
