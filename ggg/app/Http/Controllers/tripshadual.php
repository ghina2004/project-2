<?php

namespace App\Http\Controllers;

use App\Http\Requests\schadualTrip;
use App\Models\optionaljourny;
use App\Models\trip_schadual;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class tripshadual extends Controller
{
    public function AddjournySchaduals(schadualTrip $request)
    {


        $scheduleTripData = $request->only([
            'firstDayPlace1_en','firstDayPlace1_ar', 'frist_DAY_PHOTO1',
            'firstDayPlace2_en','firstDayPlace2_ar', 'frist_DAY_PHOTO2',
            'firstDayPlace3_en','firstDayPlace3_ar', 'frist_DAY_PHOTO3',
            'secondDayPlace1_en','secondDayPlace1_ar', 'second_DAY_PHOTO1',
            'secondDayPlace2_en','secondDayPlace2_ar', 'second_DAY_PHOTO2',
            'secondDayPlace3_en','secondDayPlace3_ar', 'second_DAY_PHOTO3',
            'ThirdDayPlace1_en', 'ThirdDayPlace1_ar', 'Third_DAY_PHOTO1',
            'ThirdDayPlace2_en','ThirdDayPlace2_ar', 'Third_DAY_PHOTO2',
            'ThirdDayPlace3_en','ThirdDayPlace3_ar', 'Third_DAY_PHOTO3',
            'FourthDayPlace1_en','FourthDayPlace1_ar', 'Fourth_DAY_PHOTO1',
            'FourthDayPlace2_en','FourthDayPlace2_ar', 'Fourth_DAY_PHOTO2',
            'FourthDayPlace3_en','FourthDayPlace3_ar', 'Fourth_DAY_PHOTO3',
            'FifthDayPlace1_en','FifthDayPlace1_ar', 'Fifth_DAY_PHOTO1',
            'FifthDayPlace2_en', 'FifthDayPlace2_en','Fifth_DAY_PHOTO2',
            'FifthDayPlace3_en','FifthDayPlace3_ar',  'Fifth_DAY_PHOTO3',
            'destination_en','destination_ar', 'fly_time', 'fly_date',
            'time1', 'time2', 'time3', 'time4', 'time5', 'time6',
            'time7', 'time8', 'time9', 'time10', 'time11', 'time12',
            'time13', 'time14', 'time15',
            'priceFor1Day', 'priceFor2Day', 'priceFor3Day', 'priceFor4Day', 'priceFor5Day'
        ]);

        $timeFields = [
            'time1', 'time2', 'time3', 'time4', 'time5', 'time6',
            'time7', 'time8', 'time9', 'time10', 'time11', 'time12',
            'time13', 'time14', 'time15'
        ];

        foreach ($timeFields as $field) {
            if ($request->has($field)) {
                $convertedTime = Carbon::createFromFormat('g:i A', $request->{$field})->format('H:i:s');
                $scheduleTripData[$field] = $convertedTime;
            }
        }


        $dailyTimes = [
            ['time1', 'time2', 'time3'],
            ['time4', 'time5', 'time6'],
            ['time7', 'time8', 'time9'],
            ['time10', 'time11', 'time12'],
            ['time13', 'time14', 'time15']
        ];

        foreach ($dailyTimes as $index => $times) {
            $time1 = $scheduleTripData[$times[0]];
            $time2 = $scheduleTripData[$times[1]];
            $time3 = $scheduleTripData[$times[2]];

            if ($time1 >= $time2 || $time2 >= $time3) {
                return response()->json([
                    'message' => 'You must enter the times of the activities in order (i.e. the first activity should be before the second, and so on...)',
                    'error' => [
                        'day' => $index + 1,
                        'times' => [
                            $times[0] => $time1,
                            $times[1] => $time2,
                            $times[2] => $time3
                        ]
                    ]
                ], 400);
            }
        }

        $totalPrice = array_reduce(range(1, 5), function ($carry, $day) use ($scheduleTripData) {
            return $carry + $scheduleTripData["priceFor{$day}Day"];
        }, 0);

        $scheduleTripData['Totalprice'] = $totalPrice;

        $photos = [
            'frist_DAY_PHOTO1', 'frist_DAY_PHOTO2', 'frist_DAY_PHOTO3',
            'second_DAY_PHOTO1', 'second_DAY_PHOTO2', 'second_DAY_PHOTO3',
            'Third_DAY_PHOTO1', 'Third_DAY_PHOTO2', 'Third_DAY_PHOTO3',
            'Fourth_DAY_PHOTO1', 'Fourth_DAY_PHOTO2', 'Fourth_DAY_PHOTO3',
            'Fifth_DAY_PHOTO1', 'Fifth_DAY_PHOTO2', 'Fifth_DAY_PHOTO3'
        ];

        foreach ($photos as $photo) {
            if ($request->hasFile($photo)) {
                $image = $request->file($photo);
                $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $scheduleTripData[$photo] = 'images/' . $imageName;
            }
        }


        $newTrip = trip_schadual::create($scheduleTripData);
        return response()->json($newTrip);
    }

    public function editJournySchaduals(Request $request, $tripSchadualId)
    {
        $tripSchedule = trip_schadual::find($tripSchadualId);

        if (!$tripSchedule) {
            return response()->json(['message' => 'Trip schedule not found'], 404);
        }
        $validationRules = [
            'firstDayPlace1_en' => 'regex:/^[a-zA-Z ]+$/',
            'firstDayPlace2_en' => 'regex:/^[a-zA-Z ]+$/',
            'firstDayPlace3_en' => 'regex:/^[a-zA-Z ]+$/',
            'secondDayPlace1_en' => 'regex:/^[a-zA-Z ]+$/',
            'secondDayPlace2_en' => 'regex:/^[a-zA-Z ]+$/',
            'secondDayPlace3_en' => 'regex:/^[a-zA-Z ]+$/',
            'ThirdDayPlace1_en' => 'regex:/^[a-zA-Z ]+$/',
            'ThirdDayPlace2_en' => 'regex:/^[a-zA-Z ]+$/',
            'ThirdDayPlace3_en' => 'regex:/^[a-zA-Z ]+$/',
            'FourthDayPlace1_en' => 'regex:/^[a-zA-Z ]+$/',
            'FourthDayPlace2_en' => 'regex:/^[a-zA-Z ]+$/',
            'FourthDayPlace3_en' => 'regex:/^[a-zA-Z ]+$/',
            'FifthDayPlace1_en' => 'regex:/^[a-zA-Z ]+$/',
            'FifthDayPlace2_en' => 'regex:/^[a-zA-Z ]+$/',
            'FifthDayPlace3_en' => 'regex:/^[a-zA-Z ]+$/',
            'firstDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'firstDayPlace2_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'firstDayPlace3_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'secondDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'secondDayPlace2_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'secondDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'ThirdDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'ThirdDayPlace2_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'ThirdDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FourthDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FourthDayPlace2_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FourthDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FifthDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FifthDayPlace2_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FifthDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'destination_en' => 'regex:/^[a-zA-Z ]+$/',
            'destination_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
            'fly_date' => 'date_format:Y-m-d',
            'fly_time' => 'date_format:"g:i A"',
            'expiry_Date' => 'date_format:Y-m-d|after_or_equal:today',
            'Number_of_flight_hours' => 'numeric|min:2',
            'price' => 'numeric|min:10000',
            'available_seats' => 'numeric|min:4',

            'priceFor1Day' => 'numeric|min:1000',
            'priceFor2Day' => 'numeric|min:1000',
            'priceFor3Day' => 'numeric|min:1000',
            'priceFor4Day' => 'numeric|min:1000',
            'priceFor5Day' => 'numeric|min:1000',
        ];
        $validationRules = [];
for ($i = 1; $i <= 15; $i++) {
    $validationRules['time' . $i] = 'date_format:"g:i A"|nullable'; // Using 12-hour format
}

$validatedData = $request->validate($validationRules);

$timeKeys = array_map(function ($i) {
    return 'time' . $i;
}, range(1, 15));

$modifiedTimes = array_intersect_key($validatedData, array_flip($timeKeys));


    $oldTimes = [];
    $newTimes = [];

    foreach ($timeKeys as $timeKey) {
        $oldTimes[$timeKey] = Carbon::parse($tripSchedule->$timeKey);
        $newTimes[$timeKey] = isset($modifiedTimes[$timeKey]) ? Carbon::createFromFormat('g:i A', $modifiedTimes[$timeKey]) : $oldTimes[$timeKey];
    }

    for ($day = 1; $day <= 5; $day++) {
        $time1 = "time" . (($day - 1) * 3 + 1);
        $time2 = "time" . (($day - 1) * 3 + 2);
        $time3 = "time" . (($day - 1) * 3 + 3);

        $time1Value = $newTimes[$time1];
        $time2Value = $newTimes[$time2];
        $time3Value = $newTimes[$time3];

        $timesModified = array_filter([$time1, $time2, $time3], function ($timeKey) use ($modifiedTimes) {
            return isset($modifiedTimes[$timeKey]);
        });

        $modifiedCount = count($timesModified);

        if ($modifiedCount == 1) {
            if (isset($modifiedTimes[$time1])) {
                if (!($time1Value->lt($oldTimes[$time2]) && $time1Value->lt($oldTimes[$time3]))) {
                    return response()->json([
                        'success' => false,
                        'message' => "For day $day, modified time1 must be less than time2 and time3."
                    ], 400);
                }
            } elseif (isset($modifiedTimes[$time2])) {
                if (!($time2Value->gt($oldTimes[$time1]) && $time2Value->lt($oldTimes[$time3]))) {
                    return response()->json([
                        'success' => false,
                        'message' => "For day $day, modified time2 must be greater than time1 and less than time3."
                    ], 400);
                }
            } elseif (isset($modifiedTimes[$time3])) {
                if (!($time3Value->gt($oldTimes[$time1]) && $time3Value->gt($oldTimes[$time2]))) {
                    return response()->json([
                        'success' => false,
                        'message' => "For day $day, modified time3 must be greater than time1 and time2."
                    ], 400);
                }
            }
        } elseif ($modifiedCount == 2) {
            if (isset($modifiedTimes[$time1]) && isset($modifiedTimes[$time2])) {
                if (!($time1Value->lt($time2Value) && $time2Value->lt($oldTimes[$time3]))) {
                    return response()->json([
                        'success' => false,
                        'message' => "For day $day, modified time1 must be less than time2, and time2 must be less than time3."
                    ], 400);
                }
            } elseif (isset($modifiedTimes[$time1]) && isset($modifiedTimes[$time3])) {
                if (!($time1Value->lt($oldTimes[$time2]) && $time1Value->lt($time3Value) && $time3Value->gt($time1Value))) {
                    return response()->json([
                        'success' => false,
                        'message' => "For day $day, modified time1 must be less than time2 and time3, and time3 must be greater than time1."
                    ], 400);
                }
            } elseif (isset($modifiedTimes[$time2]) && isset($modifiedTimes[$time3])) {
                if (!($time2Value->gt($oldTimes[$time1]) && $time2Value->lt($time3Value) && $time3Value->gt($time2Value))) {
                    return response()->json([
                        'success' => false,
                        'message' => "For day $day, modified time2 must be greater than time1 and less than time3, and time3 must be greater than time2."
                    ], 400);
                }
            }
        } elseif ($modifiedCount == 3) {
            if (!($time1Value->lt($time2Value) && $time2Value->lt($time3Value))) {
                return response()->json([
                    'success' => false,
                    'message' => "For day $day, modified time1 must be less than time2, time2 must be less than time3."
                ], 400);
            }
        }
    }

   foreach ($timeKeys as $timeKey) {
        $tripSchedule->$timeKey = $newTimes[$timeKey]->format('g:i A'); // Save in 12-hour format
    }
    $photos = [
        'frist_DAY_PHOTO1',
        'frist_DAY_PHOTO2',
        'frist_DAY_PHOTO3',
        'second_DAY_PHOTO1',
        'second_DAY_PHOTO2',
        'second_DAY_PHOTO3',
        'Third_DAY_PHOTO1',
        'Third_DAY_PHOTO2',
        'Third_DAY_PHOTO3',
        'Fourth_DAY_PHOTO1',
        'Fourth_DAY_PHOTO2',
        'Fourth_DAY_PHOTO3',
        'Fifth_DAY_PHOTO1',
        'Fifth_DAY_PHOTO2',
        'Fifth_DAY_PHOTO3'
    ];

    foreach ($photos as $photo) {
        if ($request->hasFile($photo)) {
            $image = $request->file($photo);
            $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;

           if ($tripSchedule->$photo) {
                $oldFilePath = public_path($tripSchedule->$photo);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $tripSchedule->$photo = $imagePath;
        }
    }
    $tripSchedule->save();

    return response()->json([
        $tripSchedule
    ]);



}
public function DeleteSchadualTrip($schadualId){

        $delete=trip_schadual::where('id',$schadualId)->forcedelete();
    if($delete){
    return response()->json([
        'message'=>'trip schadual deleted successfully'],200);
}
if (!$delete){

 return response()->json([
        'message'=>'we do not have this trip schadual '],200);
}
}
public function GetSchadualTrips()
{
    $locale = app()->getLocale();

    $firstDayPlace1Column = 'firstDayPlace1_' . $locale;
    $firstDayPlace2Column = 'firstDayPlace2_' . $locale;
    $firstDayPlace3Column = 'firstDayPlace3_' . $locale;
    $secondDayPlace1Column = 'secondDayPlace1_' . $locale;
    $secondDayPlace2Column = 'secondDayPlace2_' . $locale;
    $secondDayPlace3Column = 'secondDayPlace3_' . $locale;
    $thirdDayPlace1Column = 'ThirdDayPlace1_' . $locale;
    $thirdDayPlace2Column = 'ThirdDayPlace2_' . $locale;
    $thirdDayPlace3Column = 'ThirdDayPlace3_' . $locale;
    $fourthDayPlace1Column = 'FourthDayPlace1_' . $locale;
    $fourthDayPlace2Column = 'FourthDayPlace2_' . $locale;
    $fourthDayPlace3Column = 'FourthDayPlace3_' . $locale;
    $fifthDayPlace1Column = 'FifthDayPlace1_' . $locale;
    $fifthDayPlace2Column = 'FifthDayPlace2_' . $locale;
    $fifthDayPlace3Column = 'FifthDayPlace3_' . $locale;
    $destinationColumn = 'destination_' . $locale;

    $schaduals = trip_schadual::select(
        "{$destinationColumn} as destination",
        'fly_date',
        'fly_time',
        'time1',
        "{$firstDayPlace1Column} as firstDayPlace1",
        'frist_DAY_PHOTO1',
        'time2',
        "{$firstDayPlace2Column} as firstDayPlace2",
        'frist_DAY_PHOTO2',
        'time3',
        "{$firstDayPlace3Column} as firstDayPlace3",
        'frist_DAY_PHOTO3',
        'time4',
        "{$secondDayPlace1Column} as secondDayPlace1",
        'second_DAY_PHOTO1',
        'time5',
        "{$secondDayPlace2Column} as secondDayPlace2",
        'second_DAY_PHOTO2',
        'time6',
        "{$secondDayPlace3Column} as secondDayPlace3",
        'second_DAY_PHOTO3',
        'time7',
        "{$thirdDayPlace1Column} as thirdDayPlace1",
        'Third_DAY_PHOTO1',
        'time8',
        "{$thirdDayPlace2Column} as thirdDayPlace2",
        'Third_DAY_PHOTO2',
        'time9',
        "{$thirdDayPlace3Column} as thirdDayPlace3",
        'Third_DAY_PHOTO3',
        'time10',
        "{$fourthDayPlace1Column} as fourthDayPlace1",
        'Fourth_DAY_PHOTO1',
        'time11',
        "{$fourthDayPlace2Column} as fourthDayPlace2",
        'Fourth_DAY_PHOTO2',
        'time12',
        "{$fourthDayPlace3Column} as fourthDayPlace3",
        'Fourth_DAY_PHOTO3',
        'time13',
        "{$fifthDayPlace1Column} as fifthDayPlace1",
        'Fifth_DAY_PHOTO1',
        'time14',
        "{$fifthDayPlace2Column} as fifthDayPlace2",
        'Fifth_DAY_PHOTO2',
        'time15',
        "{$fifthDayPlace3Column} as fifthDayPlace3",
        'Fifth_DAY_PHOTO3',
        'priceFor1Day',
        'priceFor2Day',
        'priceFor3Day',
        'priceFor4Day',
        'priceFor5Day',
        'Totalprice',
        'created_at',
        'updated_at'
    )->get();

    if ($schaduals->isEmpty()) {
        return response()->json([
            'message' => 'No schedule trips found.'
        ]);
    } else {
        return response()->json($schaduals);
    }
}
public function getSpecifTripschadualForoptional($optionalId)
{
    $optional = optionaljourny::find($optionalId);
    if (!$optional) {
        return response()->json([
            'message' => 'Invalid optional trip ID.'
        ]);
    }

    $destination = $optional->destination;
    $flyDate = $optional->fly_date;
    $flyTime = $optional->fly_time;

    $locale = app()->getLocale();

    $firstDayPlace1Column = 'firstDayPlace1_' . $locale;
    $firstDayPlace2Column = 'firstDayPlace2_' . $locale;
    $firstDayPlace3Column = 'firstDayPlace3_' . $locale;
    $secondDayPlace1Column = 'secondDayPlace1_' . $locale;
    $secondDayPlace2Column = 'secondDayPlace2_' . $locale;
    $secondDayPlace3Column = 'secondDayPlace3_' . $locale;
    $thirdDayPlace1Column = 'ThirdDayPlace1_' . $locale;
    $thirdDayPlace2Column = 'ThirdDayPlace2_' . $locale;
    $thirdDayPlace3Column = 'ThirdDayPlace3_' . $locale;
    $fourthDayPlace1Column = 'FourthDayPlace1_' . $locale;
    $fourthDayPlace2Column = 'FourthDayPlace2_' . $locale;
    $fourthDayPlace3Column = 'FourthDayPlace3_' . $locale;
    $fifthDayPlace1Column = 'FifthDayPlace1_' . $locale;
    $fifthDayPlace2Column = 'FifthDayPlace2_' . $locale;
    $fifthDayPlace3Column = 'FifthDayPlace3_' . $locale;
    $destinationColumn = 'destination_' . $locale;


    $tripschadual = trip_schadual::select(
        "{$destinationColumn} as destination",
        'fly_date',
        'fly_time',
        'time1',
        "{$firstDayPlace1Column} as firstDayPlace1",
        'frist_DAY_PHOTO1',
        'time2',
        "{$firstDayPlace2Column} as firstDayPlace2",
        'frist_DAY_PHOTO2',
        'time3',
        "{$firstDayPlace3Column} as firstDayPlace3",
        'frist_DAY_PHOTO3',
        'time4',
        "{$secondDayPlace1Column} as secondDayPlace1",
        'second_DAY_PHOTO1',
        'time5',
        "{$secondDayPlace2Column} as secondDayPlace2",
        'second_DAY_PHOTO2',
        'time6',
        "{$secondDayPlace3Column} as secondDayPlace3",
        'second_DAY_PHOTO3',
        'time7',
        "{$thirdDayPlace1Column} as thirdDayPlace1",
        'Third_DAY_PHOTO1',
        'time8',
        "{$thirdDayPlace2Column} as thirdDayPlace2",
        'Third_DAY_PHOTO2',
        'time9',
        "{$thirdDayPlace3Column} as thirdDayPlace3",
        'Third_DAY_PHOTO3',
        'time10',
        "{$fourthDayPlace1Column} as fourthDayPlace1",
        'Fourth_DAY_PHOTO1',
        'time11',
        "{$fourthDayPlace2Column} as fourthDayPlace2",
        'Fourth_DAY_PHOTO2',
        'time12',
        "{$fourthDayPlace3Column} as fourthDayPlace3",
        'Fourth_DAY_PHOTO3',
        'time13',
        "{$fifthDayPlace1Column} as fifthDayPlace1",
        'Fifth_DAY_PHOTO1',
        'time14',
        "{$fifthDayPlace2Column} as fifthDayPlace2",
        'Fifth_DAY_PHOTO2',
        'time15',
        "{$fifthDayPlace3Column} as fifthDayPlace3",
        'Fifth_DAY_PHOTO3',
        'priceFor1Day',
        'priceFor2Day',
        'priceFor3Day',
        'priceFor4Day',
        'priceFor5Day',
        'Totalprice',
        'created_at',
        'updated_at'
    )->where('destination_en', 'LIKE', '%' . $destination . '%')
        ->where('fly_date', $flyDate)
        ->where('fly_time', $flyTime)
        ->get();

    if ($tripschadual->isEmpty()) {
        return response()->json([
            'message' => 'No available trip schadual in similar information.'
        ]);
    } else {
        return response()->json([
            'Available trip schadual in similar information:' => $tripschadual
        ]);
    }
}

public function choseSchadualForOptional($tripSchadualId){

    $locale = app()->getLocale();

    $firstDayPlace1Column = 'firstDayPlace1_' . $locale;
    $firstDayPlace2Column = 'firstDayPlace2_' . $locale;
    $firstDayPlace3Column = 'firstDayPlace3_' . $locale;
    $secondDayPlace1Column = 'secondDayPlace1_' . $locale;
    $secondDayPlace2Column = 'secondDayPlace2_' . $locale;
    $secondDayPlace3Column = 'secondDayPlace3_' . $locale;
    $thirdDayPlace1Column = 'ThirdDayPlace1_' . $locale;
    $thirdDayPlace2Column = 'ThirdDayPlace2_' . $locale;
    $thirdDayPlace3Column = 'ThirdDayPlace3_' . $locale;
    $fourthDayPlace1Column = 'FourthDayPlace1_' . $locale;
    $fourthDayPlace2Column = 'FourthDayPlace2_' . $locale;
    $fourthDayPlace3Column = 'FourthDayPlace3_' . $locale;
    $fifthDayPlace1Column = 'FifthDayPlace1_' . $locale;
    $fifthDayPlace2Column = 'FifthDayPlace2_' . $locale;
    $fifthDayPlace3Column = 'FifthDayPlace3_' . $locale;
    $destinationColumn = 'destination_' . $locale;

    $tripSchadual = trip_schadual::select(
        "{$destinationColumn} as destination",
        'fly_date',
        'fly_time',
        'time1',
        "{$firstDayPlace1Column} as firstDayPlace1",
        'frist_DAY_PHOTO1',
        'time2',
        "{$firstDayPlace2Column} as firstDayPlace2",
        'frist_DAY_PHOTO2',
        'time3',
        "{$firstDayPlace3Column} as firstDayPlace3",
        'frist_DAY_PHOTO3',
        'time4',
        "{$secondDayPlace1Column} as secondDayPlace1",
        'second_DAY_PHOTO1',
        'time5',
        "{$secondDayPlace2Column} as secondDayPlace2",
        'second_DAY_PHOTO2',
        'time6',
        "{$secondDayPlace3Column} as secondDayPlace3",
        'second_DAY_PHOTO3',
        'time7',
        "{$thirdDayPlace1Column} as thirdDayPlace1",
        'Third_DAY_PHOTO1',
        'time8',
        "{$thirdDayPlace2Column} as thirdDayPlace2",
        'Third_DAY_PHOTO2',
        'time9',
        "{$thirdDayPlace3Column} as thirdDayPlace3",
        'Third_DAY_PHOTO3',
        'time10',
        "{$fourthDayPlace1Column} as fourthDayPlace1",
        'Fourth_DAY_PHOTO1',
        'time11',
        "{$fourthDayPlace2Column} as fourthDayPlace2",
        'Fourth_DAY_PHOTO2',
        'time12',
        "{$fourthDayPlace3Column} as fourthDayPlace3",
        'Fourth_DAY_PHOTO3',
        'time13',
        "{$fifthDayPlace1Column} as fifthDayPlace1",
        'Fifth_DAY_PHOTO1',
        'time14',
        "{$fifthDayPlace2Column} as fifthDayPlace2",
        'Fifth_DAY_PHOTO2',
        'time15',
        "{$fifthDayPlace3Column} as fifthDayPlace3",
        'Fifth_DAY_PHOTO3',
        'priceFor1Day',
        'priceFor2Day',
        'priceFor3Day',
        'priceFor4Day',
        'priceFor5Day',
        'Totalprice',
        'created_at',
        'updated_at'
    )->find($tripSchadualId);
    if($tripSchadual){
        return response()->json([$tripSchadual], 200);
    } else {
        return response()->json(['Trip Schadual Not Found'], 404);
    }
}






}








