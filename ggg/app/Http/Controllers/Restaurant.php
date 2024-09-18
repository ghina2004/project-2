<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use App\Models\const_trip_reservation;
use App\Models\hotel;
use App\Models\optionaljourny;
use App\Models\optionaljournyReservation;
use App\Models\restaurant as ModelsRestaurant;
use App\Models\service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Restaurant extends Controller
{
    public function AddFoodToTheMenu(Request $request)
{
    $request->validate([
        'type_en' => 'required|regex:/^[a-zA-Z ]+$/',
        'F_dish_en' => 'required|regex:/^[a-zA-Z ]+$/',
        'F_price' => 'required|numeric|min:100',
        'Fphoto' => 'required|file',
        'S_dish_en' => 'required|regex:/^[a-zA-Z ]+$/',
        'S_price' => 'required|numeric|min:100',
        'Sphoto' => 'required|file',
        'T_dish_en' => 'required|regex:/^[a-zA-Z ]+$/',
        'T_price' => 'required|numeric|min:100',
        'Tphoto' => 'required|file',
        'FO_dish_en' => 'required|regex:/^[a-zA-Z ]+$/',
        'FO_price' => 'required|numeric|min:100',
        'FOphoto' => 'required|file',
        'drinks_en' => 'required|regex:/^[a-zA-Z ]+$/',
        'drinks_price' => 'required|numeric|min:100',
        'hotel_id' => 'required',
        'type_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'F_dish_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'S_dish_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'T_dish_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'FO_dish_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'drinks_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
    ]);

    $hotel = hotel::where('hotel_Name_en', $request->hotel_id)->first();

    if (!$hotel) {
        return response()->json(['message' => 'Hotel not found'], 404);
    }

    $food = new ModelsRestaurant();
    $food->hotel_id = $hotel->id;
    $food->type_en = $request->type_en;
    $food->type_ar = $request->type_ar;

    $food->F_dish_en = $request->F_dish_en;
    $food->F_dish_ar = $request->F_dish_ar;
    $food->F_price = $request->F_price;

    $photoFields = ['Fphoto', 'Sphoto', 'Tphoto', 'FOphoto'];
    foreach ($photoFields as $photoField) {
        if ($request->hasFile($photoField)) {
            $photoFile = $request->file($photoField);
            $imageName = time() . mt_rand(1000, 9999) . '.' . $photoFile->getClientOriginalExtension();
            $photoFile->move(public_path('images'), $imageName);
            $food->$photoField = 'images/' . $imageName;
        }
    }

    $food->S_dish_en = $request->S_dish_en;
    $food->S_dish_ar = $request->S_dish_ar;
    $food->S_price = $request->S_price;

    $food->T_dish_en = $request->T_dish_en;
    $food->T_dish_ar = $request->T_dish_ar;
    $food->T_price = $request->T_price;

    $food->FO_dish_en = $request->FO_dish_en;
    $food->FO_dish_ar = $request->FO_dish_ar;
    $food->FO_price = $request->FO_price;

    $food->drinks_en = $request->drinks_en;
    $food->drinks_ar = $request->drinks_ar;
    $food->drinks_price = $request->drinks_price;

    $totalPrice = $food->F_price + $food->S_price + $food->T_price + $food->FO_price + $food->drinks_price;
    $food->total_price = $totalPrice;

    $food->save();

    return response()->json([$food], 200);
}

//لغة
public function GetALLFoodTypes(){
    $locale = app()->getLocale();

    $typeColumn = 'type_' . $locale;
    $fDishColumn = 'F_dish_' . $locale;
    $sDishColumn = 'S_dish_' . $locale;
    $tDishColumn = 'T_dish_' . $locale;
    $foDishColumn = 'FO_dish_' . $locale;
    $drinksColumn = 'drinks_' . $locale;

    $type= ModelsRestaurant::select(
        'id',
        'hotel_id',
        "{$typeColumn} as type",
        "{$fDishColumn} as F_dish",
        "{$sDishColumn} as S_dish",
        "{$tDishColumn} as T_dish",
        "{$foDishColumn} as FO_dish",
        "{$drinksColumn} as drinks",
        'F_price',
        'S_price',
        'T_price',
        'FO_price',
        'total_price',
        'Fphoto',
        'Sphoto',
        'Tphoto',
        'FOphoto',
        'created_at',
        'updated_at')->get();
    if( $type->isempty()){
    return response()->json([
'sorry,Have no Food'
    ],404);}
    else{
        return response()->json([$type,200]);
    }

}
public function updateFoodMenu(Request $request, $foodId)
{
    $request->validate([
        'type' => 'regex:/^[a-zA-Z ]+$/',
        'F_dish' => 'regex:/^[a-zA-Z ]+$/',
        'F_price' => 'numeric|min:100',
        'S_dish'=>'regex:/^[a-zA-Z ]+$/',
        'S_price'=>'numeric|min:100',
        'T_dish'=>'regex:/^[a-zA-Z ]+$/',
        'T_price'=>'numeric|min:100',
        'FO_dish'=>'regex:/^[a-zA-Z ]+$/',
        'FO_price'=>'numeric|min:100',
        'drinks'=>'regex:/^[a-zA-Z ]+$/',
        'drinks_price'=>'numeric|min:100',
        'type_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
        'F_dish_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
        'S_dish_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
        'T_dish_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
        'FO_dish_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
        'drinks_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
    ]);

    $food = ModelsRestaurant::findOrFail($foodId);

    if ($request->has('hotel_id')) {
        $hotel = hotel::where('hotel_Name_en', $request->hotel_id)->first();

        if (!$hotel) {
            return response()->json(['message' => 'Cannot update. Hotel not found.'], 404);
        }

        $food->hotel_id = $hotel->id;
    }


    $food->type_en = $request->input('type_en', $food->type_en);
    $food->type_ar = $request->input('type_ar', $food->type_ar);

    $food->F_dish_en = $request->input('F_dish_en', $food->F_dish_en);
    $food->F_dish_ar = $request->input('F_dish_ar', $food->F_dish_ar);
    $food->F_price = $request->input('F_price', $food->F_price);


    $photoFields = ['Fphoto', 'Sphoto', 'Tphoto', 'FOphoto'];
    foreach ($photoFields as $photoField) {
        if ($request->hasFile($photoField)) {

            if ($food->$photoField) {

                Storage::disk('public')->delete($food->$photoField);
            }


            $photoFile = $request->file($photoField);
            $imageName = time() . mt_rand(1000, 9999) . '.' . $photoFile->getClientOriginalExtension();
            $folder = strtolower(substr($photoField, 0, -5));
            $photoFile->move(public_path('images/' . $folder), $imageName);
            $food->$photoField = 'images/' . $imageName;
        }
    }

    $food->S_dish_en = $request->input('S_dish_en', $food->S_dish_en);
    $food->S_dish_ar = $request->input('S_dish_ar', $food->S_dish_ar);
    $food->S_price = $request->input('S_price', $food->S_price);

    $food->T_dish_en = $request->input('T_dish_en', $food->T_dish_en);
    $food->T_dish_ar = $request->input('T_dish_ar', $food->T_dish_ar);
    $food->T_price = $request->input('T_price', $food->T_price);

    $food->FO_dish_en = $request->input('FO_dish_en', $food->FO_dish_en);
    $food->FO_dish_ar = $request->input('FO_dish_ar', $food->FO_dish_ar);
    $food->FO_price = $request->input('FO_price', $food->FO_price);

    $food->drinks_en = $request->input('drinks_en', $food->drinks_en);
    $food->drinks_ar = $request->input('drinks_ar', $food->drinks_ar);
    $food->drinks_price = $request->input('drinks_price', $food->drinks_price);

    $totalPrice = $food->F_price + $food->S_price + $food->T_price + $food->FO_price + $food->drinks_price;
    $food->total_price = $totalPrice;

    $food->save();

    return response()->json([
        'message' => 'Food menu updated successfully',
        $food
    ], 200);
}


public function DeleteMenu($restaurantId){
    $delete=ModelsRestaurant::where('id',$restaurantId)->forcedelete();
    if($delete){
    return response()->json([
        'message'=>'restaurant menu type deleted successfully'],200);
}
if (!$delete){

 return response()->json([
        'message'=>'we do not have this restaurant menu type '],200);
}}
public function getRestaurantRelatedToReservedHotel($userId, $reserveId, $type)
{
    $locale = app()->getLocale();

    $typeColumn = 'type_' . $locale;
    $fDishColumn = 'F_dish_' . $locale;
    $sDishColumn = 'S_dish_' . $locale;
    $tDishColumn = 'T_dish_' . $locale;
    $foDishColumn = 'FO_dish_' . $locale;
    $drinksColumn = 'drinks_' . $locale;

    if ($type == 'const') {
        $constReserve = const_trip_reservation::where('user_id', $userId)->find($reserveId);

        if ($constReserve != null) {
            $constTrip = const_trip::find($constReserve->constTrip_id);

                $hotelId = $constTrip->hotel_id;
                $restaurants = ModelsRestaurant::select(
                    'id',
                    'hotel_id',
                    "{$typeColumn} as type",
                    "{$fDishColumn} as F_dish",
                    "{$sDishColumn} as S_dish",
                    "{$tDishColumn} as T_dish",
                    "{$foDishColumn} as FO_dish",
                    "{$drinksColumn} as drinks",
                    'F_price',
                    'S_price',
                    'T_price',
                    'FO_price',
                    'total_price',
                    'Fphoto',
                    'Sphoto',
                    'Tphoto',
                    'FOphoto',
                    'created_at',
                    'updated_at')->where('hotel_id', $hotelId)->get();
                return response()->json(['message' => 'Our menu:', 'restaurants' => $restaurants], 200);

        } else {
            return response()->json(['message' => 'No const reservation found with your ID'], 404);
        }
    } elseif ($type == 'optional') {
        $optionalReserve = OptionaljournyReservation::where('user_id', $userId)->find($reserveId);

        if ($optionalReserve != null) {

                $hotelId = $optionalReserve->hotel_id;
                $restaurants = ModelsRestaurant::select(
                    'id',
                    'hotel_id',
                    "{$typeColumn} as type",
                    "{$fDishColumn} as F_dish",
                    "{$sDishColumn} as S_dish",
                    "{$tDishColumn} as T_dish",
                    "{$foDishColumn} as FO_dish",
                    "{$drinksColumn} as drinks",
                    'F_price',
                    'S_price',
                    'T_price',
                    'FO_price',
                    'total_price',
                    'Fphoto',
                    'Sphoto',
                    'Tphoto',
                    'FOphoto',
                    'created_at',
                    'updated_at')->where('hotel_id', $hotelId)->get();
                return response()->json(['message' => 'Our menu:', 'restaurants' => $restaurants], 201);

        } else {
            return response()->json(['message' => 'No optional reservation found with your ID'], 404);
        }
    } else {
        return response()->json(['message' => 'Invalid reservation type'], 400);
    }
}

public function choseDish($restaurantId,$userId,Request $request){
$restaurnat=ModelsRestaurant::find($restaurantId);
$user=user::find($userId)->first();
$service=new service();
$service->user_id=$userId;
    $locale = app()->getLocale();
    $service->Service_Name = $restaurnat->{'type_' . $locale};

$service->Number_OF_Members=$request->Number_OF_Members;
$service->total_Price=$restaurnat->total_price*$service->Number_OF_Members;
if($user->wallet<$service->total_Price){
return response()->json(['your mony in your wallet is not enough']);
}
if($user->wallet>=$service->total_Price){
    $service->save();
    $user->wallet-=$service->total_Price;
    $user->save();
    return response()->json([
        'Service_Name' => $service->Service_Name,
        'Number_OF_Members'=>$service->Number_OF_Members,
        'your invonce:'=>$service->total_Price,
        'thanks For chosing us!'
],200);




}

}


public function getAvailableReservation($userId){
    $locale = app()->getLocale();

    $destinationColumn = 'destination_' . $locale;
    $descriptionColumn = 'description_' . $locale;
    $constReserves = const_trip_reservation::where('user_id', $userId)->get();
    $optionalReserves = OptionaljournyReservation::where('user_id', $userId)->get();

    $availableReservations = [];

    foreach ($constReserves as $constReserve) {
        $constTrip = const_trip::select(
            'id',
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
            'Total_Price',
            'avg',
            'created_at',
            'updated_at',
            "{$destinationColumn} as destination",
            "{$descriptionColumn} as description")->find($constReserve->constTrip_id);
        if ($constTrip) {
            $flightDate = Carbon::parse($constTrip->fly_date);
            $fiveDaysAfterFlight = $flightDate->copy()->addDays(5);

            if (Carbon::now()->between($flightDate, $fiveDaysAfterFlight)) {
                $availableReservations[] = ['type' => 'Const', 'reservation' => $constTrip];
            }
        }
    }

    foreach ($optionalReserves as $optionalReserve) {
        $optionalJourney = Optionaljourny::select(
            'id',
            'expiry_Date',
            'fly_date',
            'fly_time',
            'Number_of_flight_hours',
            'price',
            'available_seats',
            'hotels',
            'transporation',
            'Food',
            'season_id',
            'section_id',
            'type_ticket_id',
            'continents_id',
            'Tripschadual',
            'journy_photo1',
            'journy_photo2',
            'journy_photo3',
            'created_at',
            'updated_at',
            "{$destinationColumn} as destination")->find($optionalReserve->optionaljourny_id);
        if ($optionalJourney) {
            $flightDate = Carbon::parse($optionalJourney->fly_date);
            $fiveDaysAfterFlight = $flightDate->copy()->addDays(5);

            if (Carbon::now()->between($flightDate, $fiveDaysAfterFlight)) {
                $availableReservations[] = ['type' => 'Optional', 'reservation' => $optionalJourney];
            }
        }
    }

    if (!empty($availableReservations)) {
        return response()->json(['message' => 'Available Reservations For You:', 'reservations' => $availableReservations], 202);
    } elseif ($constReserves->isNotEmpty() || $optionalReserves->isNotEmpty()) {
        return response()->json(['message' => 'Your last reservations have finished'], 400);
    } else {
        return response()->json(['message' => 'There is no reservation with your ID'], 404);
    }
}
public function chosereservation($reservationID,$type){
    if($type=='optional'){
        $optionalreservation=optionaljournyReservation::find($reservationID)->first();
        return response()->json($optionalreservation,200);
    }
    elseif($type=='const'){
        $constreservation=const_trip_reservation::find($reservationID)->first();
        return response()->json($constreservation,201);
    }
    else{
        return response()->json(['illegal type'],404);
    }
}
}
