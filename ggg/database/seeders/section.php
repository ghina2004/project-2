<?php

namespace Database\Seeders;

use App\Models\section as ModelsSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class section extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModelsSection::create([
            'section_Name_en'=>'Solo_Trip',
            'section_Name_ar'=>'رحلة فردية'

           ]);
           ModelsSection::create([

            'section_Name_en'=>'Family_Trip',
            'section_Name_ar'=>'رحلة عائلية',

           ]);
           ModelsSection::create([

            'section_Name_en'=>'Friends_Trip',
            'section_Name_ar'=>'رحلة مع الاصدقاء',

           ]);
    }
}
