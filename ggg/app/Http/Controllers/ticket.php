<?php

namespace App\Http\Controllers;

use App\Http\Requests\ticket as RequestsTicket;
use App\Models\continent;
use App\Models\season;
use App\Models\section;
use App\Models\ticket as ModelsTicket;
use App\Models\transportation;
use App\Models\type_ticket;
use Illuminate\Http\Request;

class ticket extends Controller
{
    public function AddTicket(RequestsTicket $request)
{



    $originalConstjourny = ModelsTicket::find($request->id);
    if ($originalConstjourny) {
        $originalFlyDate = $originalConstjourny->fly_date;
        $originalExpiryDate = $originalConstjourny->expiry_Date;
    } else {
        $originalFlyDate = null;
        $originalExpiryDate = null;
    }

     if ($request->has('fly_date') && $request->has('expiry_Date')) {
        if ($request->fly_date <= $request->expiry_Date) {
            return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
        }
    }

    if ($request->has('fly_date') && !$request->has('expiry_Date')) {
        if ($request->fly_date <= $originalExpiryDate) {
            return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
        }
    } elseif (!$request->has('fly_date') && $request->has('expiry_Date')) {
        if ($originalFlyDate <= $request->expiry_Date) {
            return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
        }
    }



    $ticket = new ModelsTicket();
    $ticket->destination_en = $request->destination_en;
    $ticket->destination_ar = $request->destination_ar;
    $ticket->expiry_Date = $request->expiry_Date;
    $ticket->fly_date = $request->fly_date;
    $ticket->fly_time = $request->fly_time;
    $ticket->Number_of_flight_hours = $request->Number_of_flight_hours;
    $ticket->price = $request->price;
    $ticket->available_seats = $request->available_seats;

    $continent = continent::where('continents_Name_en', $request->continents_id)
        ->orWhere('continents_Name_ar', $request->continents_id)
        ->first();
        if (!$continent) {
        return response()->json(['message' => 'Continent Not Found! You Have To Choose From: Asia, Africa, Europe, North_America, South_America, Australia'], 404);
    }
    $ticket->continents_id = $continent->id;

    $type_ticket = type_ticket::where('type_en', $request->type_ticket_id)
    ->orWhere('type_ar', $request->type_ticket_id)
    ->first();
     if (!$type_ticket) {
        return response()->json(['message' => 'Type Ticket Not Found! You Have To Choose From: OptionalTrip, ConstTrip, Ticket'], 404);
    }
    $ticket->type_ticket_id = $type_ticket->id;
    $photos = ['journy_photo1', 'journy_photo2', 'journy_photo3'];
foreach ($photos as $photo) {
    if ($request->hasFile($photo)) {
        $image = $request->file($photo);
        $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $imagePath = 'images/' . $imageName;
        $ticket->$photo = $imagePath;
    }
}

    $ticket->save();

    return response()->json([
        'message' => 'Ticket added successfully',
        'ticket' => $ticket
    ]);
}


public function editTicket(Request $request, $TicketId){
    $request->validate([
        'destination_en'=>'|regex:/^[a-zA-Z ]+$/',
        'destination_ar'=>'regex:/^[\p{Arabic}\s]+$/u',
        'expiry_Date'=>'date|after_or_equal:today',
        'fly_date'=>'date_format:Y-m-d',
        'fly_time' => 'date_format:"g:i A"',
        'Number_of_flight_hours'=>'numeric|min:3',
        'price'=>'numeric|min:1000',
        'available_seats'=>'numeric|min:1',
    ]);
    $ticket = ModelsTicket::findOrFail($TicketId);

    $originalConstjourny = ModelsTicket::find($request->id);
    if ($originalConstjourny) {
        $originalFlyDate = $originalConstjourny->fly_date;
        $originalExpiryDate = $originalConstjourny->expiry_Date;
    } else {
        $originalFlyDate = null;
        $originalExpiryDate = null;
    }

    if ($request->has('fly_date') && $request->has('expiry_Date')) {
        if ($request->fly_date <= $request->expiry_Date) {
            return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
        }
    }

    if ($request->has('fly_date') && !$request->has('expiry_Date')) {
        if ($request->fly_date <= $originalExpiryDate) {
            return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
        }
    } elseif (!$request->has('fly_date') && $request->has('expiry_Date')) {
        if ($originalFlyDate <= $request->expiry_Date) {
            return response()->json(['message' => 'Fly date must be after expiry date!'], 400);
        }
    }


    if ($request->has('destination')) {
        $ticket->destination = $request->destination;
    }
    if ($request->has('fly_date')) {
        $ticket->fly_date = $request->fly_date;
    }
    if ($request->has('fly_time')) {
        $ticket->fly_time = $request->fly_time;
    }
    if ($request->has('Number_of_flight_hours')) {
        $ticket->Number_of_flight_hours = $request->Number_of_flight_hours;
    }
    if ($request->has('price')) {
        $ticket->price = $request->price;
    }
    if ($request->has('available_seats')) {
        $ticket->available_seats = $request->available_seats;
    }

    if ($request->has('continents_id')) {
        $continent = continent::where('continents_Name_en', $request->continents_id)
        ->orWhere('continents_Name_ar', $request->continents_id)
        ->first();
        if (!$continent) {
            return response()->json(['message' => 'Continent Not Found! You Have To Choose From: Asia, Africa, Europe, North_America, South_America, Australia'], 404);
        }
        $ticket->continents_id = $continent->id;
    }

    if ($request->has('type_ticket_id')) {
        $type_ticket = type_ticket::where('type_en', $request->type_ticket_id)
        ->orWhere('type_ar', $request->type_ticket_id)
        ->first();
        if (!$type_ticket) {
            return response()->json(['message' => 'Type Ticket Not Found! You Have To Choose From: OptionalTrip, ConstTrip, Ticket'], 404);
        }
        $ticket->type_ticket_id = $type_ticket->id;
    }

    $photos = ['journy_photo1', 'journy_photo2', 'journy_photo3'];
    foreach ($photos as $photo) {
        if ($request->hasFile($photo)) {
            $image = $request->file($photo);
            $imageName = time() . mt_rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
            $ticket->$photo = $imagePath;
        }
    }

     $ticket->save();

    return response()->json([
        'message' => 'Ticket edited successfully',
        'ticket' => $ticket
    ], 200);
}

    public function DeleteTicket($ticketId){
        $delete=ModelsTicket::where('id',$ticketId)->forcedelete();
        if($delete){
        return response()->json([
            'message'=>'journy deleted successfully'],200);
    }
    else{

     return response()->json([
            'message'=>'we do not have this journy '],200);
    }


    }
    public function GetTickets()
    {
        $locale = app()->getLocale();
        $destinationColumn = 'destination_' . $locale;

        $journy = ModelsTicket::select(
            'id',
            "{$destinationColumn} as destination",
            'expiry_Date',
            'fly_date',
            'fly_time',
            'Number_of_flight_hours',
            'price',
            'available_seats',
            'continents_id',
            'type_ticket_id',
            'journy_photo1',
            'journy_photo2',
            'journy_photo3',
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

public function getticketchosen($id){
    $locale = app()->getLocale();
    $destinationColumn = 'destination_' . $locale;
    $ticket=ModelsTicket::select(
        'id',
        "{$destinationColumn} as destination",
        'expiry_Date',
        'fly_date',
        'fly_time',
        'Number_of_flight_hours',
        'price',
        'available_seats',
        'continents_id',
        'type_ticket_id',
        'journy_photo1',
        'journy_photo2',
        'journy_photo3',
        'created_at',
        'updated_at')->find($id);
    return response()->json($ticket,200);

}
}
