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
        Schema::create('transportations', function (Blueprint $table) {
            $table->id();
            $table->string('country_Name_en');
            $table->string('country_Name_ar');
            $table->string('transportation_Name_en')->nullable();
            $table->string('transportation_Name_ar')->nullable();
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
        Schema::dropIfExists('transportations');
    }
};
