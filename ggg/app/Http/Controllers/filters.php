<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use App\Models\continent;
use App\Models\optionaljourny;
use App\Models\season;
use App\Models\section;
use App\Models\ticket;
use App\Models\type_ticket;
use Illuminate\Http\Request;

class filters extends Controller
{
    public function DisplayTripsDependonFORConstTrip($idC, $idSE, $idSEC, $idtype) {
        if (empty($idC) || empty($idSE) || empty($idSEC) || empty($idtype)) {
            return response()->json([ 'All inputs are required.'], 400);
        }
        $locale = app()->getLocale();

         $trips = const_trip::where('continents_id', $idC)
            ->where('season_id', $idSE)
            ->where('section_id', $idSEC)
            ->where('type_ticket_id', $idtype)->select( 'id',
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
                 'updated_at'
             )->get();


        if ($trips->isEmpty()) {
            return response()->json(['message' => 'Wait for us! We will add new journeys soon.'], 200);
        } else {
            return response()->json($trips, 201);
        }
    }

    public function DisplayTripsDependonForOptionalTrip($idC, $idSE, $idSEC, $idtype) {
        $locale = app()->getLocale();
        $destinationColumn = 'destination_' . $locale;

         $trips = optionaljourny::where('continents_id', $idC)
            ->where('season_id', $idSE)
            ->where('section_id', $idSEC)
            ->where('type_ticket_id', $idtype)->select(
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

        // Check if the query result is empty
        if ($trips->isEmpty()) {
            return response()->json(['message' => 'Wait for us! We will add new journeys soon.'], 200);
        } else {
            return response()->json($trips, 201);
        }
    }

    public function DisplayTripsDependonFoTicket($idC, $idtype) {
        $locale = app()->getLocale();
        $destinationColumn = 'destination_' . $locale;

        $trips = ticket::where('continents_id', $idC)
            ->where('type_ticket_id', $idtype)->select(
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

         if ($trips->isEmpty()) {
            return response()->json(['message' => 'Wait for us! We will add new journeys soon.'], 200);
        } else {
            return response()->json($trips, 201);
        }
    }

    public function GetContients(){
        $locale = app()->getLocale();
        $cont=continent::select(
            'id',
            'continents_Name_' . $locale . ' as continents_Name',
        )->get();
        return response()->json(
            $cont,200);

    }
    public function GetSeasons(){
        $locale = app()->getLocale();
        $season=season::select(
            'id',
            'season_Name_' . $locale . ' as season_Name',
        )->get();
        return response()->json(
            $season
        ,200);
    }
    public function GetSections(){
        $locale = app()->getLocale();
        $section=section::select(
            'id',
            'section_Name_' . $locale . ' as section_Name',
        )->get();
    return response()->json(
        $section
    ,200);   }

    public function GetTypeTicket(){
        $locale = app()->getLocale();
        $TypeTicket=type_ticket::select(
            'id',
            'type_' . $locale . ' as type',
        )->get();
        return response()->json(
            $TypeTicket
        ,200);
    }

}
