<?php

namespace Database\Seeders;

use App\Models\continent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class continents extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        continent::create([
            'continents_Name_en'=>'Asia',
            'continents_Name_ar'=>'اسيا',

           ]);
           continent::create([

            'continents_Name_en'=>'Africa',
            'continents_Name_ar'=>'افريقيا',

           ]);
           continent::create([

            'continents_Name_en'=>'Europe',
            'continents_Name_ar'=>'اوروبا',


           ]);
           continent::create([

            'continents_Name_en'=>'North_America',
            'continents_Name_ar'=>'اميركا الشمالية'
           ]);
           continent::create([

            'continents_Name_en'=>'South_America',
            'continents_Name_ar'=>'اميركا الجنوبية',

           ]);
           continent::create([

            'continents_Name_en'=>'Australia',
            'continents_Name_ar'=>'استراليا',

           ]);
    }
}
