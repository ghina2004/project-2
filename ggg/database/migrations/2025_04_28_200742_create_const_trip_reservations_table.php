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
        Schema::create('const_trip_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('constTrip_id')->constrained('const_trips')->cascadeOnDelete();
          
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->integer('Number_of_Tickets');
            $table->double('totalPrice')->default(0);
            $table->string('confirmation')->nullable();
            $table->string('payment_status')->default('Not paid');
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
        Schema::dropIfExists('const_trip_reservations');
    }
};
