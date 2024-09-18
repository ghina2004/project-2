<?php

namespace App\Http\Controllers;

use App\Http\Requests\hotel;
use App\Http\Requests\Resquesthotel;
use App\Models\const_trip;
use App\Models\hotel as ModelsHotel;
use App\Models\optionaljourny;
use Illuminate\Http\Request;

class hotels extends Controller
{
    public function AddHotels(Resquesthotel $hotels){
        $addhotels=new ModelsHotel();
        $addhotels->country_Name_en=$hotels->country_Name_en;
        $addhotels->country_Name_ar=$hotels->country_Name_ar;
        $addhotels->hotel_Name_en=$hotels->hotel_Name_en;
        $addhotels->hotel_Name_ar=$hotels->hotel_Name_ar;
        $addhotels->Type_Reservation_en=$hotels->Type_Reservation_en;
        $addhotels->Type_Reservation_ar=$hotels->Type_Reservation_ar;
        $addhotels->description_ar=$hotels->description_ar;
        $addhotels->description_en=$hotels->description_en;
        $addhotels->price=$hotels->price;
        $addhotels->photo1=$hotels->photo1;
        $photos = ['photo1', 'photo2', 'photo3'];
        foreach ($photos as $photo) {
            $photoFile = $hotels->file($photo);
            $hotelPhotoPath = null;
            if ($hotels->hasFile($photo)) {
                $imageName = time() . mt_rand(1000, 9999) . '.' . $photoFile->getClientOriginalExtension();
                $photoFile->move(public_path('images'), $imageName);
                $hotelPhotoPath = 'images/' . $imageName;
                $addhotels->$photo = $hotelPhotoPath;

            }
        }
       $addhotels->save();
        return response()->json([$addhotels,
            'message'=>'hotel added successfully'
        ],200);
    }
    public function EditHotel(Request $request, $id)
{
    $hotel = ModelsHotel::find($id);

    if (!$hotel) {
        return response()->json([
            'message' => 'Hotel not found'
        ], 404);
    }

    $request->validate([
        'country_Name_en' => 'regex:/^[a-zA-Z ]+$/',
        'hotel_Name_en' => 'regex:/^[a-zA-Z ]+$/',
        'country_Name_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'hotel_Name_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'price' => 'numeric|min:100',
        'Type_Reservation_en' => 'regex:/^[a-zA-Z ]+$/',
        'Type_Reservation_ar' =>'required|regex:/^[\p{Arabic}\s]+$/u',
    ]);

    $oldData = $hotel->toArray();
    $photos = ['photo1', 'photo2', 'photo3'];
    $oldPhotos = [];
    foreach ($photos as $photo) {
        $photoFile = $request->file($photo);
        if ($request->hasFile($photo)) {
              $imageName = time() . mt_rand(1000, 9999) . '.' . $photoFile->getClientOriginalExtension();
            $photoFile->move(public_path('images'), $imageName);
            $hotelPhotoPath = 'images/' . $imageName;
            $hotel->$photo = $hotelPhotoPath;
        } else {
              $hotelPhotoPath = $hotel->$photo;
        }
        $oldPhotos[$photo] = $hotelPhotoPath;
    }

    foreach ($request->all() as $key => $value) {
        if ($key !== '_method' && $key !== '_token' && !in_array($key, $photos)) {
            $hotel->$key = $value;
        }
    }

    $hotel->save();

    $updatedData = array_intersect_key($hotel->toArray(), $request->all());
    $unchangedData = array_diff_assoc($oldData, $updatedData);

    return response()->json([
        'message' => 'Hotel updated successfully',
        'unchanged_data' => $unchangedData,
        'old_photos' => $oldPhotos
    ], 200);
}



public function DeleteHotel($id){

    $delete=ModelsHotel::where('id',$id)->forcedelete();
    if($delete){
    return response()->json([
        'message'=>'hotel deleted successfully'],200);
}
if (!$delete){

 return response()->json([
        'message'=>'we do not have this hotel '],200);
}
}

    public function GetHotels(){
        $locale = app()->getLocale();
        $hotels = ModelsHotel::select(
            'id',
            'country_Name_' . $locale . ' as country_Name',
            'hotel_Name_' . $locale . ' as hotel_Name',
            'Type_Reservation_' . $locale . ' as Type_Reservation',
            'description_' . $locale . ' as description',
            'price',
            'photo1',
            'photo2',
            'photo3',
            'created_at',
            'updated_at'
        )->get();
    if ($hotels->isNotEmpty()) {
        return response()->json(
             $hotels
        , 200);
    } else {
        return response()->json([
            'message' => 'You do not have available hotels'
        ], 200);
    }
}

    public function GetSpecificHotelsOptional($optionalTripId) {
        $constjourny = optionaljourny::find($optionalTripId);

        if (!$constjourny) {
            return response()->json(['message' => 'Trip not found'], 404);
        }

        $locale = app()->getLocale();
        $destinationColumn = 'destination_en' . $locale;
        $hotelCountry = $constjourny->$destinationColumn;

        $countryColumn = 'country_Name_' . $locale;
        $hotelNameColumn = 'hotel_Name_' . $locale;
        $typeReservationColumn = 'Type_Reservation_' . $locale;
        $descriptionColumn = 'description_' . $locale;

        $hotels = ModelsHotel::select(
            'id',
            "{$countryColumn} as country_Name",
            "{$hotelNameColumn} as hotel_Name",
            "{$typeReservationColumn} as Type_Reservation",
            "{$descriptionColumn} as description",
            'price',
            'photo1',
            'photo2',
            'photo3',
            'created_at',
            'updated_at'
        )->where($countryColumn, 'LIKE', "%{$hotelCountry}%")
            ->get();

        if($hotels->isNotEmpty()){
        return response()->json([
            'Available Hotels in a similar destination:' => $hotels
        ]);}
        else{
            return response()->json(['No Available Hotels in a similar destination'],404);
        }
    }


    public function GetSpecificHotelsToConst($destination)
    {
        $locale = app()->getLocale();
        $countryColumn = 'country_Name_' . $locale;
        $hotelNameColumn = 'hotel_Name_' . $locale;
        $typeReservationColumn = 'Type_Reservation_' . $locale;
        $descriptionColumn = 'description_' . $locale;

        $hotels = ModelsHotel::select(
            'id',
            "{$countryColumn} as country_Name",
            "{$hotelNameColumn} as hotel_Name",
            "{$typeReservationColumn} as Type_Reservation",
            "{$descriptionColumn} as description",
            'price',
            'photo1',
            'photo2',
            'photo3',
            'created_at',
            'updated_at'
        )->where($countryColumn, 'LIKE', "%{$destination}%")->get();
        if ($hotels->isEmpty()) {
            return response()->json([
                'message' => 'No available hotels in a similar destination.'
            ]);
        } else {
            return response()->json([
                'Available Hotels in a similar destination:' => $hotels
            ]);
        }
    }

    public function choseHotelConst($hotelId)
    {
        $locale = app()->getLocale();

        $countryColumn = 'country_Name_' . $locale;
        $hotelNameColumn = 'hotel_Name_' . $locale;
        $typeReservationColumn = 'Type_Reservation_' . $locale;
        $descriptionColumn = 'description_' . $locale;

        $hotel = ModelsHotel::select(
            'id',
            "{$countryColumn} as country_Name",
            "{$hotelNameColumn} as hotel_Name",
            "{$typeReservationColumn} as Type_Reservation",
            "{$descriptionColumn} as description",
            'price',
            'photo1',
            'photo2',
            'photo3',
            'created_at',
            'updated_at'
        )->where('id', $hotelId)->first();


        if ($hotel) {
            return response()->json([
                'message' => $hotel
            ]);
        } else {
            return response()->json(['message' => 'Not Found!']);
        }
    }


    }






