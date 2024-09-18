<?php

namespace App\Http\Controllers;

use App\Http\Requests\transportation as RequestsTransportation;
use App\Models\const_trip;
use App\Models\hotel;
use App\Models\optionaljourny;
use App\Models\ticket;
use App\Models\transportation;
use Illuminate\Http\Request;

class Transportations extends Controller
{
    public function AddTransportations(RequestsTransportation $trans){
        $addtransporations=new transportation();
        $addtransporations=new transportation();
        $addtransporations->country_Name_en=$trans->country_Name_en;
        $addtransporations->country_Name_ar=$trans->country_Name_ar;
        $addtransporations->transportation_Name_en=$trans->transportation_Name_en;
        $addtransporations->transportation_Name_ar=$trans->transportation_Name_ar;
        $addtransporations->price=$trans->price;

        $photos = ['photo1', 'photo2', 'photo3'];
foreach ($photos as $photo) {
    $photoFile = $trans->file($photo);
    $transportationPhotoPath = null;
    if ($trans->hasFile($photo)) {
        $transportationPhotoPath = md5(uniqid(rand(), true)) . '.' . $photoFile->getClientOriginalExtension();
        $photoFile->move(public_path('images'), $transportationPhotoPath);
        $transportationPhotoPath = 'images/' . $transportationPhotoPath;
    }
    $addtransporations->$photo = $transportationPhotoPath;
}
        $addtransporations->save();
        return response()->json([ $addtransporations ,
        'message'=>'transporation added successfully'
     ],200);
     }


     public function EditTransportation(Request $request, $id)
{
    $transportation = transportation::find($id);

    if (!$transportation) {
        return response()->json([
            'message' => 'Transportation not found'
        ], 404);
    }

    $request->validate([
        'country_Name_en' => 'regex:/^[a-zA-Z ]+$/',
        'country_Name_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
        'transportation_Name_en' => 'regex:/^[a-zA-Z ]+$/',
        'transportation_Name_ar' =>'required|regex:/^[\p{Arabic}\s]+$/u',
        'price' => 'numeric|min:5',
        'Number_of_rooms' => 'numeric',

    ]);

    $oldPhotos = [
        'photo1' => $transportation->photo1,
        'photo2' => $transportation->photo2,
        'photo3' => $transportation->photo3,
    ];

    $oldData = $transportation->toArray();

    foreach ($request->all() as $key => $value) {
        if ($key !== '_method' && $key !== '_token') {
            if (strpos($key, 'photo') === 0) {
                if ($request->hasFile($key)) {
                    $photoFile = $request->file($key);
                    $imageName = time() . mt_rand(1000, 9999) . '.' . $photoFile->getClientOriginalExtension();
                    $photoFile->move(public_path('images'), $imageName);
                    $transportation->$key = 'images/' . $imageName;
                } else {
                    $transportation->$key = $oldPhotos[$key];
                }
            } else {
                $transportation->$key = $value;
            }
        }
    }
    $transportation->save();

    $updatedData = array_intersect_key($transportation->toArray(), $request->all());
    $unchangedData = array_diff_assoc($oldData, $updatedData);

    return response()->json([
        $transportation
    ], 200);
}

public function DeleteTransporation($id){

    $delete=transportation::where('id',$id)->forcedelete();
    if($delete){
    return response()->json([
        'message'=>' transportation successfully'],200);
}
if (!$delete){

 return response()->json([
        'message'=>'we do not have this transportion '],200);
}
}
public function GetTransportations(){
    $locale = app()->getLocale();
    $countryColumn = 'country_Name_' . $locale;
    $transportationNameColumn = 'transportation_Name_' . $locale;

    $trans = transportation::select( 'id',
        "{$countryColumn} as country_Name",
        "{$transportationNameColumn} as transportation_Name",
        'price',
        'photo1',
        'photo2',
        'photo3',
        'created_at',
        'updated_at')->get();


if ($trans->isNotEmpty()) {
    return response()->json([
        'message' => $trans
    ], 200);
} else {
    return response()->json([
        'message' => 'You do not have available transportation'
    ], 200);
}
}

 public function GetSpecificTransportationsOptional($optionaltrip){
    $optionalTrip = optionaljourny::find($optionaltrip);

    if (!$optionalTrip) {
        return response()->json(['message' => 'Trip not found'], 404);
    }
     $locale = app()->getLocale();
     $destinationColumn = 'destination_' . $locale;
     $destination = $optionalTrip->$destinationColumn;


    // Use LIKE operator for approximate matching
     $countryColumn = 'country_Name_' . $locale;
     $transportationNameColumn = 'transportation_Name_' . $locale;

     $optionalTrip = transportation::select('id',
         "{$countryColumn} as country_Name",
         "{$transportationNameColumn} as transportation_Name",
         'price',
         'photo1',
         'photo2',
         'photo3',
         'created_at',
         'updated_at')
         ->where($countryColumn, 'LIKE', "%{$destination}%")
         ->get();
         if($optionalTrip->isNotEmpty()){
    return response()->json([
        'Available transportation in similar destination:' => $optionalTrip
    ]);}
    else{
        return response()->json(['No Available transportation in similar destination'],404);
    }
 }

     public function GetSpecificTransportationsConst($destination)
{
    $locale = app()->getLocale();

        $countryColumn = 'country_Name_' . $locale;
        $transportationNameColumn = 'transportation_Name_' . $locale;

        $constTrip = transportation::select('id',
            "{$countryColumn} as country_Name",
            "{$transportationNameColumn} as transportation_Name",
            'price',
            'photo1',
            'photo2',
            'photo3',
            'created_at',
            'updated_at')
            ->where($countryColumn, 'LIKE', '%' . $destination . '%')
            ->get();

    if ($constTrip->isEmpty()) {
        return response()->json([
            'message' => 'No available transportation in similar destinations.'
        ]);
    } else {
        return response()->json([
            'Available Hotels in similar destinations:' => $constTrip
        ]);
    }
}

     public function choseTransportationConst($transporationId){

        $locale = app()->getLocale();
         $countryColumn = 'country_Name_' . $locale;
         $transportationNameColumn = 'transportation_Name_' . $locale;

        $trans=transportation::select('id',
            "{$countryColumn} as country_Name",
            "{$transportationNameColumn} as transportation_Name",
            'price',
            'photo1',
            'photo2',
            'photo3',
            'created_at',
            'updated_at')
            ->where('id', $transporationId)
            ->first();
if($trans){

         return response()->json([
             'messsage:'=>$trans ],200);}
         else {
            return response()->json(['message' => 'Not Found!'],404);
        }}

        public function GetSpecificTransportationsTicket($Ticketid){
            $TicketId = ticket::find($Ticketid);

            if (!$TicketId) {
                return response()->json(['message' => 'ticket not found'], 404);
            }
            $locale = app()->getLocale();
            $countryNameColumn = 'country_Name_' . $locale;
            $transportationNameColumn = 'transportation_Name_' . $locale;


            $destination = $TicketId->destination;

            $ticket= transportation::select('id',
                "{$countryNameColumn} as country_Name",
                "{$transportationNameColumn} as transportation_Name",
                'price',
                'photo1',
                'photo2',
                'photo3',
                'created_at',
                'updated_at')
                ->where($countryNameColumn, 'LIKE', '%' . $destination . '%')
                ->get();
            if($ticket->isNotEmpty()){
            return response()->json([
                'Available transportation in similar destination:' => $ticket
            ]);}
            else{
                return response()->json(['No Available transportation in similar destination'],404);
            }
         }

public function choseTransportationticket($transportationId){
    $locale = app()->getLocale();
    $countryNameColumn = 'country_Name_' . $locale;
    $transportationNameColumn = 'transportation_Name_' . $locale;

    $trans=transportation::select('id',
        "{$countryNameColumn} as country_Name",
        "{$transportationNameColumn} as transportation_Name",
        'price',
        'photo1',
        'photo2',
        'photo3',
        'created_at',
        'updated_at')
        ->where('id', $transportationId)
        ->first();

     return response()->json([
        $trans
     ],200);

}






 }

