<?php

namespace App\Http\Controllers;

use App\Http\Requests\cutomserMony;
use App\Models\const_trip;
use App\Models\const_trip_reservation;
use App\Models\hotel;
use App\Models\optionaljourny;
use App\Models\optionaljournyReservation;
use App\Models\service;
use App\Models\taxi;
use App\Models\ticket;
use App\Models\ticket_reservation;
use App\Models\transportation;
use App\Models\trip_schadual;
use App\Models\User;
use Illuminate\Http\Request;

class Reservations extends Controller
{
    public function selectHotel($hotelId){
$hotel=hotel::find($hotelId);
if($hotel){
return response()->json([
    'hotel:'=>$hotel
]);}
else{
    return response()->json(['hotel not found'],404);
}


    }
    public function selectTransportation($transportationId){
$transportation=transportation::find($transportationId);
if($transportation){
return response()->json([
    'transportation:'=>$transportation
],200);}
else{
    return response()->json(['Not found!'],404);
}


    }
    public function selectSchadualTrip($tripSchadualId){
        $tripSchadual=trip_schadual::find($tripSchadualId)->get();
        return response()->json([
            'tripschadual:'=>$tripSchadual
        ]);
    }

    public function OptionalJournyReservation($userId, $optionaljournyId, $hotelId, $transportationId, $tripSchadualId, Request $request)
{
    $request->validate([
        'Number_of_Tickets'=>'required|numeric|min:1',

    ]);
    $user = User::find($userId);

    if (!$user) {
        return response()->json([
            'message' => 'Invalid user ID.'
        ], 400);
    }

    // التحقق من وجود رحلة اختيارية بالمعرف المرسل
    $optional = optionaljourny::find($optionaljournyId);
    if (!$optional) {
        return response()->json([
            'message' => 'Invalid optional journey ID.'
        ], 400);
    }

    // التحقق من وجود فندق بالمعرف المرسل
    $hotel = hotel::find($hotelId);
    if (!$hotel) {
        return response()->json([
            'message' => 'Invalid hotel ID.'
        ], 400);
    }

    $trans = transportation::find($transportationId);
    if (!$trans) {
        return response()->json([
            'message' => 'Invalid transportation ID.'
        ], 400);
    }

    $tripschadual = trip_schadual::find($tripSchadualId);
    if (!$tripschadual) {
        return response()->json([
            'message' => 'Invalid trip schadual ID.'
        ], 400);
    }

    $availableTickets = $optional->available_seats;
    if ($availableTickets <= 0 || $availableTickets === 'sold out') {
        return response()->json([
            'message' => "Tickets for this trip are sold out. The trip will be updated soon."
        ], 400);
    }

    if ($request->Number_of_Tickets > $availableTickets) {
        return response()->json([
            'message' => "Requested number of tickets exceeds available seats. Available tickets: $availableTickets"
        ], 400);
    }

    $optional->available_seats -= $request->Number_of_Tickets;
    if ($optional->available_seats == 0) {
        $optional->available_seats = 'sold out';
    }
    $optional->save();

    $totalPrice = $hotel->price + $trans->price + ($optional->price * $request->Number_of_Tickets) + $tripschadual->price;

    $optionalReservation = new optionaljournyReservation();
    $optionalReservation->user_id = $userId;
    $optionalReservation->hotel_id = $hotelId;
    $optionalReservation->optionaljourny_id = $optionaljournyId;
    $optionalReservation->transportaion_id = $transportationId;
    $optionalReservation->tripschadual_id = $tripSchadualId;
    $optionalReservation->Number_of_Tickets = $request->Number_of_Tickets;
    $optionalReservation->price_of_journy = $optional->price;
    $optionalReservation->totalPrice = $totalPrice;
    $optionalReservation->save();

    return response()->json([
        // 'hotel Name:' => $hotel->hotel_Name,
        // 'transportation:' => $trans->transportation_Name,
        // 'Number Of tickets:' => $optionalReservation->Number_of_Tickets,
        // 'Total price:' => $totalPrice
        $optionalReservation
    ], 200);
}


public function confirmationForOptional(Request $request,$reservId){
    if ($request->input('confirmation') == 'yes'){

$optionalreservation= optionaljournyReservation::find($reservId);

$optionalreservation->confirmation='yes';
$optionalreservation->save();
return response()->json([
    'confirmed successfully'
]);
    }
    if($request->input('confirmation') == 'No'){
        $optionalreservation= optionaljournyReservation::find($reservId);
        $optionalreservation->confirmation='No';
        $optionalreservation->save();
        $delete=optionaljournyReservation::find($reservId)->forcedelete();
return response()->json([
    'the booking has been canceled'
]);
    }


}
public function PaymentOptional(Request $request,$reserveId,$userId){
    $optionalreserve=optionaljournyReservation::find($reserveId);
    $user=User::find($userId);
    if($request->input('payment_status') == 'From Wallet'){
        if($user->wallet>=$optionalreserve->totalPrice){
$user->wallet-=$optionalreserve->totalPrice;
$user->save();
$optionalreserve->payment_status='paid From Wallet';
$optionalreserve->save();
return response()->json([
    'you paid:'=>$optionalreserve->totalPrice,
    'your wallet now:'=>$user->wallet,

]);
    }
    if($user->wallet<$optionalreserve->totalPrice){
return response()->json([
    'you do not have enough mony in your wallete'
]);
    }}
    if($request->input('payment_status') == 'manual'){
        $optionalreserve->payment_status='manual Not paid';
        $optionalreserve->save();
        return response()->json([
            'you have to pay until five days else we will delete your reservation'
        ]);
    }

}

public function updatepaymentStatusByAdminForManualPaymentFoOptional(Request $request, $userId, $optionalreserveId) {
    $request->validate([
        'payment_status'=>'required|regex:/^[a-zA-Z ]+$/',

    ]);
    $optionalReservation = OptionalJournyReservation::where('user_id', $userId)
                                                    ->where('id', $optionalreserveId)
                                                    ->first();
     if ($optionalReservation) {
        if($optionalReservation->payment_status == 'manual Not paid'){
if($request->input('payment_status') == 'manual paid'){
           $optionalReservation->payment_status = 'manual paid';
            $optionalReservation->save();

             return response()->json([
                'message' => 'Payment status edited successfully'
            ]);
        }
    }
    else
return response()->json([
    'check your payment status'
]);
}

     return response()->json([
        'error' => 'No matching optional reservation found for the given user ID and reservation ID'
    ]);
}

public function constTripReservation(Request $request, $userId, $constTripId)
{
    $request->validate([
        'Number_of_Tickets'=>'required|numeric|min:1',

    ]);
    $user=User::find($userId)->first();
    if(!$user){
        return response()->json(['User Not Found'],404);
    }
    $consttrip = const_trip::find($constTripId);

    if (!$consttrip) {
        return response()->json([
            'message' => "Constant trip not found.",
        ], 404);
    }

    if ($consttrip->available_seats === 0 || $consttrip->available_seats === 'soldout') {
        return response()->json([
            'message' => "Tickets for this trip are not available. We will update the tickets soon.",
        ], 400);
    }

    if ($request->Number_of_Tickets > $consttrip->available_seats) {
        return response()->json([
            'message' => "Requested number of tickets exceeds available seats. Available tickets: " . $consttrip->available_seats,
        ], 400);
    }

    $consttrip->available_seats -= $request->Number_of_Tickets;

    if ($consttrip->available_seats === 0) {
        $consttrip->available_seats = 'soldout';
    }

    $consttrip->save();

    $constTripReservation = new const_trip_reservation();
    $constTripReservation->user_id =  $userId;
    $constTripReservation->constTrip_id = $constTripId;
    $constTripReservation->Number_of_Tickets = $request->Number_of_Tickets;

      $constTripReservation->totalPrice = $consttrip->Total_Price * $constTripReservation->Number_of_Tickets;


    $constTripReservation->save();
$hotelname=hotel::find($consttrip->hotel_id);
$tansportationname=transportation::find($consttrip->transportation_id);
    return response()->json([

        'hotel Name:' => $hotelname->hotel_Name_en,
        'transportation:' => $tansportationname->transportation_Name_en,
        'Number Of tickets:' => $constTripReservation->Number_of_Tickets,
        'Total price:' => $constTripReservation->totalPrice,

    ], 200);
}



public function confirmationForConstTrip(Request $request,$reservId){
    if ($request->input('confirmation') == 'yes'){

$optionalreservation= const_trip_reservation::find($reservId);
$optionalreservation->confirmation='yes';
$optionalreservation->save();
return response()->json([
    'confirmed successfully'
]);
    }
    if($request->input('confirmation') == 'No'){
        $optionalreservation= const_trip_reservation::find($reservId);
        $optionalreservation->confirmation='No';
        $optionalreservation->save();
        $delete=const_trip_reservation::find($reservId)->forcedelete();
return response()->json([
    'the booking has been canceled'
]);
    }


}
public function PaymentConst(Request $request,$reserveId,$userId){
    $constreserve=const_trip_reservation::find($reserveId);
    $user=User::find($userId);
    if($request->input('payment_status') == 'From Wallet'){
        if($user->wallet>=$constreserve->totalPrice){
$user->wallet-=$constreserve->totalPrice;
$user->save();
$constreserve->payment_status='paid From Wallet';
$constreserve->save();
return response()->json([
    'you paid:'=>$constreserve->totalPrice,
    'your wallet now:'=>$user->wallet,

]);
    }
    if($user->wallet<$constreserve->totalPrice){
return response()->json([
    'you do not have enough mony in your wallete'
]);
    }}
    if($request->input('payment_status') == 'manual'){
        $constreserve->payment_status='manual Not paid';
        $constreserve->save();
        return response()->json([
            'you have to pay until five days else we will delete your reservation'
        ]);
    }

}

public function updatepaymentStatusByAdminForManualPaymentForConst(Request $request, $constreserveId, $userId) {
    $request->validate([
        'payment_status'=>'required|regex:/^[a-zA-Z ]+$/',

    ]);
    $constReservation = const_trip_reservation::where('user_id', $userId)
                                                   ->where('id', $constreserveId)
                                                   ->first();

    if ($constReservation) {
        if($constReservation->payment_status=='manual Not paid'){
        if ($request->input('payment_status') == 'manual paid') {
          $constReservation->payment_status = 'manual paid';
           $constReservation->save();

            return response()->json([
               'message' => 'Payment status edited successfully'
           ]);
       }
   }

else
return response()->json([
    'check your payment status'
]);}

    return response()->json([
       'error' => 'No matching const reservation found for the given user ID and reservation ID'
   ]);
}
public function ticketReservation(Request $request, $userId, $tivketID, $transportationId) {
    $request->validate([
        'Number_of_Tickets' => 'required|numeric|min:1',
    ]);

    $ticket = ticket::find($tivketID);
    if (!$ticket) {
        return response()->json(['message' => 'Ticket not found'], 404);
    }

    $user = user::find($userId);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $transportation = transportation::find($transportationId);
    if (!$transportation) {
        return response()->json(['message' => 'Transportation not found'], 404);
    }

    // فرضية: يوجد لدى التذكرة حقل 'available_seats' للتحقق من توافر المقاعد
    if ($ticket->available_seats === 0 || $ticket->available_seats === 'soldout') {
        return response()->json([
            'message' => 'Tickets for this event are not available. We will update the tickets soon.',
        ], 400);
    }

    if ($request->Number_of_Tickets > $ticket->available_seats) {
        return response()->json([
            'message' => 'Requested number of tickets exceeds available seats. Available tickets: ' . $ticket->available_seats,
        ], 400);
    }

    // تحديث عدد المقاعد المتاحة
    $ticket->available_seats -= $request->Number_of_Tickets;

    if ($ticket->available_seats === 0) {
        $ticket->available_seats = 'soldout';
    }

    $ticket->save();

    $ticketReservation = new ticket_reservation();
    $ticketReservation->user_id = $userId;
    $ticketReservation->transportaion_id = $transportationId;
    $ticketReservation->ticket_id = $tivketID;
    $ticketReservation->Number_of_Tickets = $request->Number_of_Tickets;
    $ticketReservation->totalPrice = $ticket->price * $request->Number_of_Tickets;
    $ticketReservation->save();

    return response()->json([
        'ticketReservation' => $ticketReservation,
    ]);
}
public function confirmationForTicket(Request $request,$reservId){
    if ($request->input('confirmation') == 'yes'){

$ticketreservation= ticket_reservation::find($reservId);
$ticketreservation->confirmation='yes';
$ticketreservation->save();
return response()->json([
    'confirmed successfully'
]);
    }
    if($request->input('confirmation') == 'No'){
        $ticketlreservation= ticket_reservation::find($reservId);
        $ticketlreservation->confirmation='No';
        $ticketlreservation->save();
        $delete=ticket_reservation::find($reservId)->forcedelete();
return response()->json([
    'the booking has been canceled'
]);
    }


}
public function Paymentticket(Request $request,$reserveId,$userId){
    $ticketreserve=ticket_reservation::find($reserveId);
    if(!$ticketreserve){
        return response()->json(['ticket reservation not found'],404);
    }

    $user=User::find($userId);
    if(!$user){
        return response()->json(['user not found'],404);
    }
    if($request->input('payment_status') == 'From Wallet'){
        if($user->wallet>=$ticketreserve->totalPrice){
$user->wallet-=$ticketreserve->totalPrice;
$user->save();
$ticketreserve->payment_status='paid From Wallet';
$ticketreserve->save();
return response()->json([
    'you paid:'=>$ticketreserve->totalPrice,
    'your wallet now:'=>$user->wallet,

]);
    }
    if($user->wallet<$ticketreserve->totalPrice){
return response()->json([
    'you do not have enough mony in your wallete'
]);
    }}
    if($request->input('payment_status') == 'manual'){
        $ticketreserve->payment_status='manual Not paid)';
        $ticketreserve->save();
        return response()->json([
            'you have to pay until five days else we will delete your reservation'
        ]);
    }

}
public function updatepaymentStatusByAdminForManualPaymentForticket(Request $request, $userId, $ticketreserveId) {
    $request->validate([
        'payment_status'=>'required|regex:/^[a-zA-Z ]+$/',

    ]);
    $ticketReservation = ticket_reservation::where('user_id', $userId)
                                                   ->where('id', $ticketreserveId)
                                                   ->first();

    if ($ticketReservation) {
        if($ticketReservation->payment_status=='manual Not paid'){
        if ($request->input('payment_status') == 'manual paid') {
          $ticketReservation->payment_status = 'manual paid';
           $ticketReservation->save();

            return response()->json([
               'message' => 'Payment status edited successfully'
           ]);
       }
   }
else
return response()->json([
    'check your payment status'
]);}

    return response()->json([
       'error' => 'No matching ticket reservation found for the given user ID and reservation ID'
   ]);
}
public function AddMonyToTheWallete($userId, Request $money)
{
    $money->validate([
        'wallet'=>'required|numeric|min:100'


]);
    $user = User::find($userId);

    if (!$user) {
        return response()->json([ 'User not found'], 404);
    }

    $charge = $money->wallet;
    $user->wallet += $money->wallet;
    $user->save();

    return response()->json([
        'message' => 'You charged to the customer wallet',
        'charged_amount' => $charge,
        'total_wallet_amount' => $user->wallet
    ]);
}
public function reservationsForUser($userID)
{
    $user = User::find($userID);

    if (!$user) {
        return response()->json(['message' => 'Invalid User!'], 404);
    }

    $reservations = [
        'Optional Journy Reservations' => optionaljournyReservation::where('user_id', $userID)->get(),
        'Ticket Reservations' => ticket_reservation::where('user_id', $userID)->get(),
        'Const Trip Reservations' => const_trip_reservation::where('user_id', $userID)->get(),
    ];

    $resultsByType = [];
    $anyReservationsFound = false;

    foreach ($reservations as $type => $reservation) {
        if (!$reservation->isEmpty()) {
            $reservationWithDestinations = $reservation->map(function ($res) use ($type) {
                if ($type == 'Optional Journy Reservations') {
                    $journey = optionaljourny::find($res->optionaljourny_id);
                    $res->destination_en = $journey ? $journey->destination_en : null;
                    $res->destination_ar= $journey ? $journey->destination_ar : null;
                } elseif ($type == 'Ticket Reservations') {
                    $ticket = ticket::find($res->ticket_id);
                    $res->destination_en = $ticket ? $ticket->destination_en : null;
                    $res->destination_ar = $ticket ? $ticket->destination_ar : null;
                } elseif ($type == 'Const Trip Reservations') {
                    $trip = const_trip::find($res->constTrip_id);
                    $res->destination_en = $trip ? $trip->destination_en : null;
                    $res->destination_ar = $trip ? $trip->destination_en : null;
                }
                return $res;
            });

            $resultsByType[$type] = $reservationWithDestinations->values();
            $anyReservationsFound = true;
        } else {
            $resultsByType[$type] = [];
        }
    }

    if (!$anyReservationsFound) {
        return response()->json(['message' => 'No reservations found for this user.'], 404);
    }

    return response()->json(['reservations' => $resultsByType], 200);
}
}

















