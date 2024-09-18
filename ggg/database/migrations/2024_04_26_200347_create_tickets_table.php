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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('destination_en')->nullable();
            $table->string('destination_ar')->nullable();
         $table->date('expiry_Date')->nullable();
         $table->date('fly_date');
         $table->string('fly_time');
         $table->string('Number_of_flight_hours');
         $table->double('price')->nullable();
         $table->string('available_seats')->nullable();
         $table->foreignId('continents_id')->constrained('continents')->cascadeOnDelete()->nullable();
        $table->foreignId('type_ticket_id')->constrained('type_tickets')->cascadeOnDelete();
         $table->string('journy_photo1');
         $table->string('journy_photo2');
         $table->string('journy_photo3');
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
        Schema::dropIfExists('tickets');
    }
};
