<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

        /*public function up(): void
        {
            Schema::table('requests', function (Blueprint $table) {
                $table->bigInteger('status')->nullable();
            });
        }

        public function down(): void
        {
            Schema::table('requests', function (Blueprint $table) {
                $table->bigInteger('status')->nullable(); // Revertir cambios aquí si es necesario
            });
        }*/
        /*public function up(): void
        {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('updatedDateTime');
                $table->dropColumn('expirationDateTime');
                $table->dropColumn('registrationDateTime');
                $table->dropColumn('locationDescription');
                $table->dropColumn('longitude');
                $table->dropColumn('latitude');
            });
        }

        public function down(): void
        {
            Schema::table('requests', function (Blueprint $table) {

            });
        }*/
        /*public function up(): void
        {
            Schema::table('services', function (Blueprint $table) {
                /*$table->datetime('createdDateTime')->nullable();
                $table->datetime('updatedDateTime')->nullable();
                $table->datetime('finishDateTime')->nullable();*/
                /*$table->dropColumn('programDate');
                $table->dropColumn('requestsDate');
                $table->dropColumn('finishedDate');
            });
        }

        public function down(): void
        {
            Schema::table('services', function (Blueprint $table) {
              /*  $table->datetime('createdDateTime')->nullable();
                $table->datetime('updatedDateTime')->nullable();
                $table->datetime('finishDateTime')->nullable();*/
            /*});
        }*/
    /**
     * Reverse the migrations.
     */
    /* public function up(): void
        {
            Schema::table('detail_technical_agenda', function (Blueprint $table) {
                $table->dropColumn('citationId')->nullable();
                $table->dropColumn('typeJob');
                $table->dropColumn('citationDate')->nullable();
            });
        }

        public function down(): void
        {
            Schema::table('detail_technical_agenda', function (Blueprint $table) {

            });
        }*/
        public function up(): void
        {
           Schema::table('publicity', function (Blueprint $table) {
                $table->unsignedBigInteger('categoryId')->nullable();
                $table->foreign('categoryId')->references('id')->on('category_publicity')->onDelete('cascade');
            });
        }

        //public function down(): void
        //{
          ///  Schema::table('publicity', function (Blueprint $table) {
                //$table->unsignedBigInteger('categoryId')->nullable();
                //$table->foreign('categoryId')->references('id')->on('category_publicity')->onDelete('cascade');
            // });
       //S }
};
