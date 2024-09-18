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
        Schema::create('optionaljournies', function (Blueprint $table) {
            $table->id();
            $table->string('destination_en')->nullable();
            $table->string('destination_ar')->nullable();
         $table->date('expiry_Date')->nullable();
         $table->date('fly_date');
         $table->string('fly_time');
         $table->string('Number_of_flight_hours');
         $table->double('price')->nullable();
         $table->string('available_seats')->nullable();
         $table->string('hotels')->default('we have list of Available Hotels,you Can chose if you want');
         $table->string('transporation')->default(' we have (cars ,planes,boats) if you want you can chose');
         $table->string('Food')->default('you can take food from the hotel restaurants you have chosen');
         $table->foreignId('season_id')->constrained('seasons')->nullable();
         $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete()->nullable();
         $table->foreignId('type_ticket_id')->constrained('type_tickets')->cascadeOnDelete()->nullable();
         $table->foreignId('continents_id')->constrained('continents')->cascadeOnDelete()->nullable();
         $table->string('Tripschadual')->default('please chose your schadual');
         $table->string('journy_photo1')->nullable();
         $table->string('journy_photo2')->nullable();
         $table->string('journy_photo3')->nullable();
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
        Schema::dropIfExists('optionaljournies');
    }
};
