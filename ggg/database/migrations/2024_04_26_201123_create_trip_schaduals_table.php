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
        Schema::create('trip_schaduals', function (Blueprint $table) {
            $table->id();
            $table->string('destination_en')->nullable();
            $table->string('destination_ar')->nullable();
             $table->date('fly_date')->nullable();
             $table->string('fly_time')->nullable();
             $table->string('time1')->nullable();
             $table->string('firstDayPlace1_en')->nullable();
            $table->string('firstDayPlace1_ar')->nullable();
             $table->string('frist_DAY_PHOTO1')->nullable();
             $table->string('time2')->nullable();
             $table->string('firstDayPlace2_en')->nullable();
            $table->string('firstDayPlace2_ar')->nullable();
             $table->string('frist_DAY_PHOTO2')->nullable();
             $table->string('time3')->nullable();
             $table->string('firstDayPlace3_en')->nullable();
            $table->string('firstDayPlace3_ar')->nullable();
             $table->string('frist_DAY_PHOTO3')->nullable();
             $table->double('priceFor1Day')->nullable();
             $table->string('time4')->nullable();
             $table->string('secondDayPlace1_en')->nullable();
            $table->string('secondDayPlace1_ar')->nullable();
             $table->string('second_DAY_PHOTO1')->nullable();
             $table->string('time5')->nullable();
             $table->string('secondDayPlace2_en')->nullable();
            $table->string('secondDayPlace2_ar')->nullable();
             $table->string('second_DAY_PHOTO2')->nullable();
             $table->string('time6')->nullable();
             $table->string('secondDayPlace3_en')->nullable();
            $table->string('secondDayPlace3_ar')->nullable();
             $table->string('second_DAY_PHOTO3')->nullable();
             $table->double('priceFor2Day')->nullable();
             $table->string('time7')->nullable();
             $table->string('ThirdDayPlace1_en')->nullable();
            $table->string('ThirdDayPlace1_ar')->nullable();
             $table->string('Third_DAY_PHOTO1')->nullable();
             $table->string('time8')->nullable();
             $table->string('ThirdDayPlace2_en')->nullable();
            $table->string('ThirdDayPlace2_ar')->nullable();
             $table->string('Third_DAY_PHOTO2')->nullable();
             $table->string('time9')->nullable();
             $table->string('ThirdDayPlace3_en')->nullable();
            $table->string('ThirdDayPlace3_ar')->nullable();
             $table->string('Third_DAY_PHOTO3')->nullable();
             $table->double('priceFor3Day')->nullable();
             $table->string('time10')->nullable();
             $table->string('FourthDayPlace1_en')->nullable();
            $table->string('FourthDayPlace1_ar')->nullable();
             $table->string('Fourth_DAY_PHOTO1')->nullable();
             $table->string('time11')->nullable();
             $table->string('FourthDayPlace2_en')->nullable();
            $table->string('FourthDayPlace2_ar')->nullable();
             $table->string('Fourth_DAY_PHOTO2')->nullable();
             $table->string('time12')->nullable();
             $table->string('FourthDayPlace3_en')->nullable();
            $table->string('FourthDayPlace3_ar')->nullable();
             $table->string('Fourth_DAY_PHOTO3')->nullable();
             $table->double('priceFor4Day')->nullable();
             $table->string('time13')->nullable();
             $table->string('FifthDayPlace1_en')->nullable();
            $table->string('FifthDayPlace1_ar')->nullable();
             $table->string('Fifth_DAY_PHOTO1')->nullable();
             $table->string('time14')->nullable();
             $table->string('FifthDayPlace2_en')->nullable();
            $table->string('FifthDayPlace2_ar')->nullable();
             $table->string('Fifth_DAY_PHOTO2')->nullable();
             $table->string('time15')->nullable();
             $table->string('FifthDayPlace3_en')->nullable();
            $table->string('FifthDayPlace3_ar')->nullable();
             $table->string('Fifth_DAY_PHOTO3')->nullable();
             $table->double('priceFor5Day')->nullable();
            $table->double('Totalprice');

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
        Schema::dropIfExists('trip_schaduals');
    }
};
