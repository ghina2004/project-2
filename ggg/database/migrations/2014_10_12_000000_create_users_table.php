<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fcm_token')->nullable();
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('phone_number')->nullable();
            $table->rememberToken();
            $table->date('email_verified_at')->nullable();
            $table->softDeletes();
            $table->string('status_money')->default('money in wallet');
            $table->timestamps();
            $table->boolean('role')->default(0);
            $table->double('wallet')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
