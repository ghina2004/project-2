<?php

namespace App\Http\Controllers;

use App\Http\Requests\constjourny as RequestsConstjourny;
use App\Models\const_trip;
use App\Models\const_trip_reservation;
use App\Models\continent;
use App\Models\hotel;
use App\Models\season;
use App\Models\section;
use App\Models\transportation;
use App\Models\trip_schadual;
use App\Models\type_ticket;
use Illuminate\Http\Request;

class consTjourny extends Controller
{


    public function AddConstJourny(RequestsConstjourny $request, $transportationId, $hotelId,$tripSchadualId) {
        $constjourny = new const_trip();


        $originalConstjourny = const_trip::find($request->id);
        if ($originalConstjourny) {
            $originalFlyDate = $originalConstjourny->fly_date;
            $originalExpiryDate = $originalConstjourny->expiry_Date;
        } else {
            $originalFlyDate = null;
            $originalExpiryDate = null;
        }


        $hotel = hotel::find($hotelId)->first();
        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found!'], 404);
        }

        $transportation = transportation::find($transportationId)->first();
        if (!$transportation) {
            return response()->json(['message' => 'Transportation not found!'], 404);
        }
$tripSchadual=trip_schadual::find($tripSchadualId)->first();
if(!$tripSchadual){
    return response()->json(['message' => 'Trip Schadual not found!'], 404);

}

       $constjourny->destination_en = $request->destination_en;
        $constjourny->destination_ar = $request->destination_ar;
        $constjourny->expiry_Date = $request->expiry_Date;
        $constjourny->hotel_id = $hotelId;
        $constjourny->transportation_id = $transportationId;
        $constjourny->fly_date = $request->fly_date;
        $constjourny->fly_time = $request->fly_time;
        $constjourny->Number_of_flight_hours = $request->Number_of_flight_hours;
        $constjourny->price = $request->price;
        $constjourny->available_seats = $request->available_seats;
        $constjourny->tripschadual_id=$tripSchadualId;
        $constjourny->descripyion_en=$request->descripyion_en;
        $constjourny->descripyion_ar=$request->descripyion_ar;
        $continent = continent::where('continents_Name_en', $request->continents_id)
        ->orWhere('continents_Name_ar', $request->continents_id)
        ->first();
        if (!$continent) {
            return response()->json(['message' => 'Contient Not Found!,You Have To Chose From :Asia,Africa,Europe,North_America,South_America,Australia'], 404);
        }
        $constjourny->continents_id = $continent->id;

        $season = season::where('season_Name_en', $request->season_id)
        ->orWhere('season_Name_ar', $request->season_id)
        ->first();
        if (!$season) {
            return response()->json(['message' => 'Season Not Found!,You Have To Chose From :Spring,Summer,Autumn,Winter'], 404);
        }
        $constjourny->season_id = $season->id;
        $section = section::where('section_Name_en', $request->section_id)
        ->orWhere('section_Name_ar', $request->section_id)
        ->first();
        if (!$section ){
            return response()->json(['message' => 'Section Not Found!.You Have To Chose From:Solo_Trip,Family_Trip,Friends_Trip'], 404);
        }
        $constjourny->section_id = $section->id;

        $type_ticket = type_ticket::where('type_en', $request->type_ticket_id)
        ->orWhere('type_ar', $request->type_ticket_id)
        ->first();

        if (!$type_ticket) {
            return response()->json(['message' => 'Type Ticket Not Found!,You Have To Chose From:OptionalTri,ConstTrip,Ticket'], 404);
        }
        $constjourny->type_ticket_id = $type_ticket->id;
        $constjourny->Total_Price = $constjourny->price + $tripSchadual->Totalprice;

        if (isset($hotel->price)) {
            $constjourny->Total_Price += $hotel->price;
        }
        if (isset($transportation->price)) {
            $constjourny->Total_Price += $transportation->price;
        }// Validate dates
        if ($request->has('fly_date') && !$request->has('expiry_Date')) {
            if ($request->fly_date <= $originalExpiryDate) {
                return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
            }
        } elseif (!$request->has('fly_date') && $request->has('expiry_Date')) {
            if ($originalFlyDate <= $request->expiry_Date) {
                return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
            }
        }elseif ($request->has('fly_date') && $request->has('expiry_Date')) {
            if ($request->fly_date <= $request->expiry_Date) {
                return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
            }
        }
        $photos = ['photo1', 'photo2', 'photo3'];
foreach ($photos as $photo) {
    if ($request->hasFile($photo)) {
        $image = $request->file($photo);
        $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $imagePath = 'images/' . $imageName;
        $constjourny->$photo = $imagePath;
    }
}
   $constjourny->save();

        return response()->json(['message' => 'Journey details added successfully',$constjourny], 200);
    }
    function EditConstJourny(Request $request, $constJournyId, $hotelId, $transportationId,$tripschadualId)
{
    $request->validate([
        'destination_en' => 'regex:/^[a-zA-Z ]+$/',
        'destination_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
        'fly_date' => 'date_format:Y-m-d',
        'fly_time' => 'date_format:"g:i A"',
        'expiry_Date' => 'date_format:Y-m-d|after_or_equal:today',
        'Number_of_flight_hours' => 'numeric|min:2',
        'price' => 'numeric|min:10000',
        'available_seats' => 'numeric|min:4',

        'descripyion_en' => 'regex:/^[a-zA-Z ]+$/',
        'descripyion_ar' => 'regex:/^[\p{Arabic}\s]+$/u',
        'Total_Price' => 'numeric|min:10000'
    ]);

    $constjourny = const_trip::findOrFail($constJournyId);
    $originalFlyDate = $constjourny->fly_date;
    $originalExpiryDate = $constjourny->expiry_Date;

    $hotel = hotel::find($hotelId);
    if (!$hotel) {
        return response()->json(['message' => 'Hotel not found!'], 404);
    }

     $transportation = transportation::find($transportationId);
    if (!$transportation) {
        return response()->json(['message' => 'Transportation not found!'], 404);
    }
    $tripschadual=trip_schadual::find($tripschadualId);
    if(!$tripschadual){
        return response()->json(['Trip Schadual Not Found'],404);

    }

    $fieldsToUpdate = [
        'destination', 'fly_date', 'fly_time', 'expiry_Date', 'Number_of_flight_hours',
        'price', 'available_seats',
        'descripyion'
    ];

    foreach ($fieldsToUpdate as $field) {
        if ($request->has($field)) {
            $constjourny->$field = $request->$field;
        }
    }

    if ($request->has('continents_id')) {
        $continent = continent::where('continents_Name_en', $request->continents_id)
        ->orWhere('continents_Name_ar', $request->continents_id)
        ->first();
        if (!$continent) {
            return response()->json(['message' => 'Continent Not Found!,You Have To Chose From :Asia,Africa,Europe,North_America,South_America,Australia'], 404);
        }
        $constjourny->continents_id = $continent->id;
    }

    if ($request->has('season_id')) {
        $season = season::where('season_Name_en', $request->season_id)
        ->orWhere('season_Name_ar', $request->season_id)
        ->first();
         if (!$season) {
            return response()->json(['message' => 'Season Not Found!,You Have To Chose From :Spring,Summer,Autumn,Winter'], 404);
        }
        $constjourny->season_id = $season->id;
    }
    if ($request->has('section_id')) {
        $section = section::where('section_Name_en', $request->section_id)
        ->orWhere('section_Name_ar', $request->section_id)
        ->first();
        if (!$section) {
            return response()->json(['message' => 'Section Not Found!.You Have To Chose From:Solo_Trip,Family_Trip,Friends_Trip'], 404);
        }
        $constjourny->section_id = $section->id;
    }

    if ($request->has('type_ticket_id')) {
        $type_ticket = type_ticket::where('type_en', $request->type_ticket_id)
        ->orWhere('type_ar', $request->type_ticket_id)
        ->first();
        if (!$type_ticket) {
            return response()->json(['message' => 'Type Ticket Not Found!,You Have To Chose From:OptionalTri,ConstTrip,Ticket'], 404);
        }
        $constjourny->type_ticket_id = $type_ticket->id;
    }

    $photos = ['photo1', 'photo2', 'photo3'];

    $oldPhotoPaths = [
        'photo1' => $constjourny->photo1,
        'photo2' => $constjourny->photo2,
        'photo3' => $constjourny->photo3,
    ];

    foreach ($photos as $photo) {
        $imagePath = $oldPhotoPaths[$photo];

        if ($request->hasFile($photo)) {
            $image = $request->file($photo);
            $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
        }

         $constjourny->$photo = $imagePath;
    }


    $constjourny->save();

    return response()->json($constjourny);
}




     public function GetconstTrips(){
         $locale = app()->getLocale();
        $journy = const_trip::select( 'id',
            'destination_' . $locale . ' as destination',
            'fly_date',
            'fly_time',
            'Number_of_flight_hours',
            'price',
            'available_seats',
            'hotel_id',
            'transportation_id',
            'season_id',
            'section_id',
            'continents_id',
            'type_ticket_id',
            'tripschadual_id',
            'descripyion_' . $locale . ' as descripyion',
            'Total_Price',
            'avg',
            'created_at',
            'updated_at'
        )->get();

        if ($journy->isNotEmpty()){

        return response()->json([
            'journies'=>$journy
        ],200);}
        else{
            return response()->json([
                'message'=>'you do not have journies '
            ],200);
    }
     }
     public function CalculateTotalPriceForConst($consttripId, $hotelId, $transportationId,$tripshadualId)
{
    $consttrip = const_trip::find($consttripId);
    $hotel = hotel::find($hotelId);
    $transportation = transportation::find($transportationId);
$tripSchadual=trip_schadual::find($tripshadualId);
    if ($consttrip && $hotel && $transportation&&$tripSchadual) {
        $consttrip->Total_Price = $consttrip->price + $hotel->price + $transportation->price+$tripSchadual->Totalprice;
        $consttrip->save();

        return response()->json([
            'totalPrice' => $consttrip->Total_Price
        ]);
    } else {
        return response()->json(['message' => 'Some thing went wrong,be sure of your information']);
    }
}

    public function DeleteConstJourny($constTripId){
        $delete=const_trip::where('id',$constTripId)->forcedelete();
        if($delete){
        return response()->json([
            'message'=>'optional journy deleted successfully'],200);
    }
    if (!$delete){

     return response()->json([
            'message'=>'we do not have this optional journy '],200);
    }
    }
public function getconstchosen($constId){
    $locale = app()->getLocale();
$constId=const_trip::select( 'id',
    'destination_' . $locale . ' as destination',
    'fly_date',
    'fly_time',
    'Number_of_flight_hours',
    'price',
    'available_seats',
    'hotel_id',
    'transportation_id',
    'season_id',
    'section_id',
    'continents_id',
    'type_ticket_id',
    'tripschadual_id',
    'descripyion_' . $locale . ' as descripyion',
    'Total_Price',
    'avg',
    'photo1',
    'photo2',
    'photo3',
    'created_at',
    'updated_at')->find($constId);
return response()->json($constId,200);
}
public function getconst($id){
    $const = const_trip::where('id',$id)->get()->first();
if ($const) {
    return response()->json(
        $const
    , 200);
} else {
    return response()->json([
        'message' => 'You do not have available const trips'
    ], 404);
}
}





}
