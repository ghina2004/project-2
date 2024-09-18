<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('country_Name_en');
            $table->string('country_Name_ar');
            $table->string('hotel_Name_en');
            $table->string('hotel_Name_ar');
            $table->string('Type_Reservation_en');
            $table->string('Type_Reservation_ar');
            $table->string('description_en');
            $table->string('description_ar');
            $table->double('price')->nullable();
            $table->string('photo1')->nullable();
            $table->string('photo2')->nullable();
            $table->string('photo3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotels');
    }
};
