<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use App\Models\const_trip_reservation;
use App\Models\optionaljourny;
use App\Models\optionaljournyReservation;
use App\Models\ticket;
use App\Models\ticket_reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CancellationReservationController extends Controller
{
    public function cancellationReservationConstJourney($id)
    {
        $user = Auth::user();
        $reservation = const_trip_reservation::find($id);

        // التحقق من وجود الحجز
        if (!$reservation) {
            return response()->json([
                'status' => 0,
                'message' => trans('Reservation not found')
            ], 404);
        }
        $constTrip = const_trip::find($reservation->constTrip_id);

        if (!$constTrip) {
            return response()->json([
                'status' => 0,
                'message' => trans('Related trip not found')
            ], 404);
        }

        $currentDate = Carbon::now();
        $reservationDate = Carbon::parse($reservation->created_at);
        $daysSinceReservation = $currentDate->diffInDays($reservationDate);

        $flyDate = Carbon::parse($constTrip->fly_date);
    ;
        $daysUntilFlyDate = $currentDate->diffInDays($flyDate);

        if ($daysSinceReservation < 2 && $daysUntilFlyDate > 4) {
            if ($reservation->payment_status == 'From Wallet') {
                $refundAmount = $reservation->totalPrice;
                 $user['wallet'] += $refundAmount;
                $user->save();
            }
            $reservation->delete();

            return response()->json([
                'status' => 1,
                'data' => $user,
                'message' => trans('Reservation cancelled successfully and the money was returned')
            ], 200);
        } elseif ($daysSinceReservation >= 2 && $daysSinceReservation < 4 && $daysUntilFlyDate > 4) {
            if ($reservation->payment_status == 'From Wallet') {
                $refundAmount = $reservation->totalPrice / 2;
                $user['wallet'] += $refundAmount;
                $user->save();
            }
            $reservation->delete();

            return response()->json([
                'status' => 1,
                'data' => $user,
                'message' => trans('Reservation cancelled successfully and half of the money was returned')
            ], 200);
        } else {
            $reservation->delete();

            return response()->json([
                'status' => 1,
                'data' => $user,
                'message' => trans('Reservation cancelled successfully')
            ], 200);
        }
    }

    public function cancellationReservationOptionalJourny($id){
        $user = Auth::user();
        $reservation =optionaljournyReservation::query()->find($id);
        if (!$reservation) {
            return response()->json([
                'status' => 0,
                'message' => trans('Reservation not found')
            ], 404);
        }

        $optionalJourney = optionaljourny::find($reservation->optionaljourny_id);
        if (!$optionalJourney) {
            return response()->json([
                'status' => 0,
                'message' => trans('Related trip not found')
            ], 404);
        }

        $current_date = Carbon::now();
        $reservation_date = Carbon::parse($reservation['created_at']);

        $days_difference = $current_date->diffInDays($reservation_date);

        $fly_date = Carbon::parse($optionalJourney['fly_date']) ;

        $different =$current_date->diffInDays($fly_date);
        if ($days_difference <2 && $different >4 ) {
            if($reservation['payment_status']=='From Wallet'){
                $mony=$reservation['totalPrice'];
                $user['wallet']+= $mony;
                $user->save();
            }
            $reservation->delete();
            return response()->json([
                'status' => 1,
                'data' =>$user,
                'message'=>trans('cancelled Reservation successfully && The money was returned') ],200);
        }
        elseif ($days_difference >= 2 && $days_difference <4 && $different >4) {
            if($reservation['payment_status']=='From Wallet'){
                $mony=$reservation['totalPrice']/2;
                $user['wallet']+= $mony;
                $user->save();}

            $reservation->delete();
            return response()->json([
                'status' => 1,
                'data' =>$user,
                'message'=>trans('cancelled Reservation successfully&& The half money was returned') ],200);
        }
        else{
            $reservation->delete();
            return response()->json([
                'status' => 1,
                'data' =>$user,
                'message'=>trans('cancelled Reservation successfully')],200);
        }
    }

    public function cancellationReservationTicket($id){
        $user = Auth::user();
        $reservation =ticket_reservation::query()->find($id);
        if (!$reservation) {
            return response()->json([
                'status' => 0,
                'message' => trans('Reservation not found')
            ], 404);
        }

        $ticket = Ticket::find($reservation->ticket_id);
        if (!$ticket) {
            return response()->json([
                'status' => 0,
                'message' => trans('Related trip not found')
            ], 404);
        }


        $current_date = Carbon::now();
        $reservation_date = Carbon::parse($reservation['created_at']);
        $days_difference = $current_date->diffInDays($reservation_date);

        $fly_date = Carbon::parse($ticket['fly_date']) ;
        $different =$current_date->diffInDays($fly_date);
        if ($days_difference <2 && $different >4 ) {
            if($reservation['payment_status']=='From Wallet'){
                $mony=$reservation['totalPrice'];
                $user['wallet']+= $mony;
                $user->save();
            }
            $reservation->delete();
            return response()->json([
                'status' => 1,
                'data' =>$user,
                'message'=>trans('cancelled Reservation successfully && The money was returned') ],200);
        }
        elseif ($days_difference > 2 && $days_difference <4 && $different >4) {
            if($reservation['payment_status']=='From Wallet'){
                $mony=$reservation['totalPrice']/2;
                $user['wallet']+= $mony;
                $user->save();}

            $reservation->delete();
            return response()->json([
                'status' => 1,
                'data' =>$user,
                'message'=>trans('cancelled Reservation successfully&& The half money was returned') ],200);
        }
        else{
            $reservation->delete();
            return response()->json([
                'status' => 1,
                'data' =>$user,
                'message'=>trans('cancelled Reservation successfully')],200);
        }
    }














}
