<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {  User::query()->create([
        'first_name' => 'fareed',
        'middle_name' => 'mohammed',
        'last_name' => 'alloh',
        'email' => 'fareed@gmail.com',
        'password' => 'fffgggbbb',
        'role'=> '1'
        ]);
    }
}
