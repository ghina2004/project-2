<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\const_trip_reservation;
use App\Models\trip_schadual;
use App\Models\optionaljourny;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendActivityNotification;

class SendTripNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-trip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for trip activities that are about to start';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trips = trip_schadual::whereDate('fly_date', Carbon::today()->subDay())->get();
       // dd($trips);
        foreach ($trips as $trip) {
            $this->checkAndSendNotification($trip);

    }
        return 0;
}
protected function checkAndSendNotification($trip)
{
    $activities = [

        ['time' => 'time1', 'place_en' => 'firstDayPlace1_en', 'photo' => 'frist_DAY_PHOTO1', 'day' => 1],
        ['time' => 'time2', 'place_en' => 'firstDayPlace2_en', 'photo' => 'frist_DAY_PHOTO2', 'day' => 1],
        ['time' => 'time3', 'place_en' => 'firstDayPlace3_en','photo' => 'frist_DAY_PHOTO3', 'day' => 1],

        ['time' => 'time4', 'place_en' => 'secondDayPlace1_en', 'photo' => 'second_DAY_PHOTO1', 'day' => 2],
        ['time' => 'time5', 'place_en' => 'secondDayPlace2_en', 'photo' => 'second_DAY_PHOTO2', 'day' => 2],
        ['time' => 'time6', 'place_en' => 'secondDayPlace3_en', 'photo' => 'second_DAY_PHOTO3', 'day' => 2],

        ['time' => 'time7', 'place_en' => 'ThirdDayPlace1_en', 'photo' => 'Third_DAY_PHOTO1', 'day' => 3],
        ['time' => 'time8', 'place_en' => 'ThirdDayPlace2_en', 'photo' => 'Third_DAY_PHOTO2', 'day' => 3],
        ['time' => 'time9', 'place_en' => 'ThirdDayPlace3_en', 'photo' => 'Third_DAY_PHOTO3', 'day' => 3],

        ['time' => 'time10', 'place_en' => 'FourthDayPlace1_en', 'photo' => 'Fourth_DAY_PHOTO1', 'day' => 4],
        ['time' => 'time11', 'place_en' => 'FourthDayPlace2_en', 'photo' => 'Fourth_DAY_PHOTO2', 'day' => 4],
        ['time' => 'time12', 'place_en' => 'FourthDayPlace3_en', 'photo' => 'Fourth_DAY_PHOTO3', 'day' => 4],

        ['time' => 'time13', 'place_en' => 'FifthDayPlace1_en','photo' => 'Fifth_DAY_PHOTO1', 'day' => 5],
        ['time' => 'time14', 'place_en' => 'FifthDayPlace2_en', 'photo' => 'Fifth_DAY_PHOTO2', 'day' => 5],
        ['time' => 'time15', 'place_en' => 'FifthDayPlace3_en', 'photo' => 'Fifth_DAY_PHOTO3', 'day' => 5],
    ];


    foreach ($activities as $activity) {
        $activityTime = $trip->{$activity['time']};
        //$activityTime = $trip->time15;
        $currentDateTime = now();
      //  $futureDateTime = $currentDateTime->copy()->addMinutes(2);


      Log::info('Current DateTime: ' . $currentDateTime->toDateTimeString());
      Log::info('Activity Time: ' . $activityTime);
      
      //  if (!empty($activityTime) && $currentDateTime == $activityTime) {
            $activityDetails = [
                'title' => "Upcoming Activity on Day {$activity['day']}",
                'description' => $trip->{$activity['place_en']},
               // 'photo' => $trip->{$activity['photo']}
            ];
            SendActivityNotification::dispatch($trip,new const_trip_reservation,new optionaljourny, $activityDetails);
       // }

    }
}}
