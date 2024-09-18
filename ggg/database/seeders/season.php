<?php

namespace Database\Seeders;

use App\Models\season as ModelsSeason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class season extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModelsSeason::create([
            'season_Name_en'=>'Spring',
            'season_Name_ar'=>'ربيع',

           ]);
           ModelsSeason::create([

            'season_Name_en'=>'Summer',
            'season_Name_ar'=>'صيف'

           ]);
           ModelsSeason::create([

            'season_Name_en'=>'Autumn',
            'season_Name_ar'=>'خريف'

           ]);
           ModelsSeason::create([

            'season_Name_en'=>'Winter',
            'season_Name_ar'=>'شتاء'

           ]);

    }
}
