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
        Schema::create('const_trips', function (Blueprint $table) {
            $table->id();
            $table->string('destination_en')->nullable();
            $table->string('destination_ar')->nullable();
             $table->date('expiry_Date')->nullable();
             $table->date('fly_date');
             $table->string('fly_time');
             $table->string('Number_of_flight_hours');
             $table->double('price')->nullable();
             $table->string('available_seats')->nullable();
             $table->foreignId('hotel_id')->constrained('hotels')->nullable();
             $table->foreignId('transportation_id')->constrained('transportations')->nullable();
             $table->foreignId('season_id')->constrained('seasons')->nullable();
             $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete()->nullable();
             $table->foreignId('continents_id')->constrained('continents')->cascadeOnDelete()->nullable();
            $table->foreignId('type_ticket_id')->constrained('type_tickets')->cascadeOnDelete();
            $table->foreignId('tripschadual_id')->constrained('trip_schaduals')->nullable();
            $table->string('descripyion_en');
            $table->string('descripyion_ar');
             $table->double('Total_Price')->nullable();
            $table->float('avg')->default(0);
            $table->string('photo1');
            $table->string('photo2');
            $table->string('photo3');
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
        Schema::dropIfExists('const_trips');
    }
};
