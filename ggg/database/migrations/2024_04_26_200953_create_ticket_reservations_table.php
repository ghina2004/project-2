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
        Schema::create('ticket_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->foreignId('transportaion_id')->constrained('transportations')->nullable();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->integer('Number_of_Tickets');
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
        Schema::dropIfExists('ticket_reservations');
    }
};
