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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('type_en');
            $table->string('type_ar');
            $table->string('F_dish_en');
            $table->string('F_dish_ar');
            $table->double('F_price');
            $table->string('Fphoto');
            $table->string('S_dish_en');
            $table->string('S_dish_ar');
            $table->double('S_price');
            $table->string('Sphoto');
            $table->string('T_dish_en');
            $table->string('T_dish_ar');
            $table->double('T_price');
            $table->string('Tphoto');
            $table->string('FO_dish_en');
            $table->string('FO_dish_ar');
            $table->double('FO_price');
            $table->string('FOphoto');
            $table->string('drinks_en');
            $table->double('drinks_price');
            $table->string('drinks_ar');
            $table->double('total_price');
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
        Schema::dropIfExists('restaurants');
    }
};
