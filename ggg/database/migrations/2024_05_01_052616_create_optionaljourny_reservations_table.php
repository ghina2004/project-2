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
        Schema::create('optionaljourny_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->foreignId('optionaljourny_id')->constrained('optionaljournies')->nullable();
            $table->foreignId('tripschadual_id')->constrained('trip_schaduals')->nullable();
              $table->integer('Number_of_Tickets');
              $table->foreignId('hotel_id')->constrained('hotels')->nullable();
              $table->foreignId('transportaion_id')->constrained('transportations')->nullable();
              $table->double('price_of_journy');
              $table->double('totalPrice')->default(0);
              $table->string('confirmation')->nullable();
              $table->string('payment_status')->default('not paid');
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
        Schema::dropIfExists('optionaljourny_reservations');
    }
};
