<?php

namespace App\Http\Controllers;

use App\Http\Requests\optionaljourny as RequestsOptionaljourny;
use App\Models\continent;
use App\Models\optionaljourny as ModelsOptionaljourny;
use App\Models\season;
use App\Models\section;
use App\Models\trip_schadual;
use App\Models\type_ticket;
use Exception;
use Illuminate\Http\Request;

class optionaljourny extends Controller
{
    public function AddOptionalJourny(RequestsOptionaljourny $journy) {
        $addjourny = new ModelsOptionaljourny();
        $addjourny->destination_en = $journy->destination_en;
        $addjourny->destination_ar = $journy->destination_ar;
        $addjourny->expiry_Date = $journy->expiry_Date;
        $addjourny->fly_date = $journy->fly_date;
        $addjourny->fly_time = $journy->fly_time;
        $addjourny->Number_of_flight_hours = $journy->Number_of_flight_hours;
        $addjourny->price = $journy->price;
        $addjourny->available_seats = $journy->available_seats;

        $continent = continent::where('continents_Name_en', $journy->continents_id)
        ->orWhere('continents_Name_ar', $journy->continents_id)
        ->first();
        if (!$continent) {
            return response()->json(['message' => 'Contient Not Found!,You Have To Chose From :Asia,Africa,Europe,North_America,South_America,Australia'], 404);
        }
        $addjourny->continents_id = $continent->id;

        $season = season::where('season_Name_en', $journy->season_id)
        ->orWhere('season_Name_ar', $journy->season_id)
        ->first();
        if (!$season) {
            return response()->json(['message' => 'Season Not Found!,You Have To Chose From :Spring,Summer,Autumn,Winter'], 404);
        }
        $addjourny->season_id = $season->id;

        $section = section::where('section_Name_en', $journy->section_id)
        ->orWhere('section_Name_ar', $journy->section_id)
        ->first();
         if (!$section) {
            return response()->json(['message' => 'Section Not Found!.You Have To Chose From:Solo_Trip,Family_Trip,Friends_Trip'], 404);
        }
        $addjourny->section_id = $section->id;

        $type_ticket = type_ticket::where('type_en', $journy->type_ticket_id)
        ->orWhere('type_ar', $journy->type_ticket_id)
        ->first();
        if (!$type_ticket) {
            return response()->json(['message' => 'Type Ticket Not Found!,You Have To Chose From:OptionalTri,ConstTrip,Ticket'], 404);
        }
        $addjourny->type_ticket_id = $type_ticket->id;

        // Check if the flight date is after the expiry date
        if ($journy->fly_date <= $journy->expiry_Date) {
            return response()->json(['message' => 'Invalid flight date. Flight date must be after the expiry date.'], 400);
        }

        $photos = ['journy_photo1', 'journy_photo2', 'journy_photo3'];
        foreach ($photos as $photo) {
            if ($journy->hasFile($photo)) {
                $image = $journy->file($photo);
                $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $imagePath = 'images/' . $imageName;
                $addjourny->$photo = $imagePath;
            }
        }


        $addjourny->save();

        return response()->json(['message' => 'added successfully', $addjourny], 200);
    }



    public function EditOptionalJourny(Request $request, $journyId)
{
    $request->validate([
        'destination_en' => 'regex:/^[a-zA-Z ]+$/',
        'destination_ar' =>'regex:/^[\p{Arabic}\s]+$/u',
        'expiry_Date' => 'date|after_or_equal:today',
        'price' => 'numeric|min:1000',
        'available_seats' => 'numeric|min:1',
        'fly_date' => 'date_format:Y-m-d',
        'fly_time' => 'date_format:"g:i A"',
        'Number_of_flight_hours' => 'numeric|min:3'
    ]);

    $journy = ModelsOptionaljourny::find($journyId);
    if (!$journy) {
        return response()->json([
            'message' => 'Journy not found'
        ], 404);
    }

    $fillableAttributes = $journy->getFillable();
    $originalExpiryDate = $journy->expiry_Date;
    $originalFlyDate = $journy->fly_date;

    foreach ($request->all() as $key => $value) {
        if (in_array($key, $fillableAttributes)) {
            $journy->$key = $value;
        }
    }

    if ($request->has('expiry_Date') || $request->has('fly_date')) {
        if ($request->has('expiry_Date') && $request->has('fly_date')) {
            $newExpiryDate = $request->expiry_Date;
            $newFlyDate = $request->fly_date;

            if (strtotime($newExpiryDate) > strtotime($newFlyDate)) {
                return response()->json(['message' => 'Fly date must be after expiry date'], 401);
            }
        } elseif ($request->has('expiry_Date')) {
            $newExpiryDate = $request->expiry_Date;

            if (strtotime($newExpiryDate) > strtotime($journy->fly_date)) {
                return response()->json(['message' => 'Fly date must be after expiry date'], 402);
            }
        } elseif ($request->has('fly_date')) {
            $newFlyDate = $request->fly_date;

            if (strtotime($journy->expiry_Date) > strtotime($newFlyDate)) {
                return response()->json(['message' => 'Fly date must be after expiry date'], 403);
            }
        }
    }

    if ($request->has('continents_id')) {
        $continent = continent::where('continents_Name_en', $journy->continents_id)
        ->orWhere('continents_Name_ar', $journy->continents_id)
        ->first();
        if (!$continent) {
            return response()->json(['message' => 'Contient Not Found!,You Have To Chose From :Asia,Africa,Europe,North_America,South_America,Australia'], 404);
        }
        $journy->continents_id = $continent->id;
    }

    if ($request->has('season_id')) {
        $season = season::where('season_Name_en', $journy->season_id)
        ->orWhere('season_Name_ar', $journy->season_id)
        ->first();
        if (!$season) {
            return response()->json(['message' => 'Season Not Found!,You Have To Chose From :Spring,Summer,Autumn,Winter'], 404);
        }
        $journy->season_id = $season->id;
    }

    if ($request->has('section_id')) {
        $section = section::where('section_Name_en', $journy->section_id)
        ->orWhere('section_Name_ar', $journy->section_id)
        ->first();
        if (!$section) {
            return response()->json(['message' => 'Section Not Found!.You Have To Chose From:Solo_Trip,Family_Trip,Friends_Trip'], 404);
        }
        $journy->section_id = $section->id;
    }

    if ($request->has('type_ticket_id')) {
        $type_ticket = type_ticket::where('type_en', $journy->type_ticket_id)
        ->orWhere('type_ar', $journy->type_ticket_id)
        ->first();
        if (!$type_ticket) {
            return response()->json(['message' => 'Type Ticket Not Found!,You Have To Chose From:OptionalTri,ConstTrip,Ticket'], 404);
        }
        $journy->type_ticket_id = $type_ticket->id;
    }

    $photos = ['journy_photo1', 'journy_photo2', 'journy_photo3'];

     $oldPhotoPaths = [
        'journy_photo1' => $journy->journy_photo1,
        'journy_photo2' => $journy->journy_photo2,
        'journy_photo3' => $journy->journy_photo3,
    ];

    foreach ($photos as $photo) {
        $imagePath = $oldPhotoPaths[$photo];
        if ($request->hasFile($photo)) {
            $image = $request->file($photo);
            $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
        }
        $journy->$photo = $imagePath;
    }


$journy->save();



    return response()->json([$journy,
        'message' => 'Journy details have been updated successfully'
    ], 200);
}
    public function DeleteOptionalJourny($journyId){
        $delete=ModelsOptionaljourny::where('id',$journyId)->forcedelete();
    if($delete){
    return response()->json([
        'message'=>'optional journy deleted successfully'],200);
}
if (!$delete){

 return response()->json([
        'message'=>'we do not have this optional journy '],200);
}
}
    public function GetoptionalJournies(){
        $locale = app()->getLocale();
        $destinationColumn = 'destination_' . $locale;

        $journy = ModelsOptionaljourny::select(
            'id',
            "{$destinationColumn} as destination",
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
            'updated_at'
        )->get();

if ($journy->isNotEmpty()){
return response()->json([
'journies'=>$journy
],200);
} else {
return response()->json([
'message'=>'you do not have journies '
],200);
}
    }
public function getoptionaltripschosen($id){
    $locale = app()->getLocale();
    $destinationColumn = 'destination_' . $locale;
    $optional=ModelsOptionaljourny::select(
        'id',
        "{$destinationColumn} as destination",
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
        'updated_at')->find($id);
    return response()->json($optional,200);
}

}





