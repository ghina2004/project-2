<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use Illuminate\Http\Request;

class recentJournies extends Controller
{
    public function GetLastJournies(){
        $locale = app()->getLocale();

        $destinationColumn = 'destination_' . $locale;
        $descriptionColumn = 'descripyion_' . $locale;

        $flights = const_trip::select(
            'id',
            "{$destinationColumn} as destination",
            'expiry_Date',
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
            "{$descriptionColumn} as description",
            'Total_Price',
            'avg',
            'photo1',
            'photo2',
            'photo3',
            'created_at',
            'updated_at'
        )->orderBy('created_at', 'desc')->get();

        if( $flights->isNotEmpty()){
return response()->json(
    $flights
,200);}

    else{
return response()->json(['there is no recent journies'],404);
    }
}
}
