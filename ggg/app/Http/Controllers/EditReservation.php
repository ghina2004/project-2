<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use App\Models\const_trip_reservation;
use App\Models\hotel;
use App\Models\optionaljourny;
use App\Models\optionaljournyReservation;
use App\Models\ticket;
use App\Models\ticket_reservation;
use App\Models\transportation;
use App\Models\trip_schadual;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EditReservation extends Controller
{

    public function EditOptionalJournyReservation($userId, $reservationId, $optionaljournyId, $hotelId, $transportationId, $tripSchadualId, Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'Number_of_Tickets' => 'nullable|numeric|min:1',
    ]);

    // جلب حجز الرحلة الإختياري
    $optionalReservation = optionaljournyReservation::find($reservationId);
    if (!$optionalReservation || $optionalReservation->user_id != $userId) {
        return response()->json(['message' => 'Reservation not found or unauthorized access'], 404);
    }

    // جلب بيانات الرحلة الإختياري
    $optionalJourney = optionaljourny::find($optionaljournyId);
    if ($optionalJourney->available_seats === 'sold out') {
        return response()->json(['message' => 'The trip is sold out. New trips will be added soon! Stay tuned.'], 202);
    }

    // جلب بيانات النقل والفندق وجدول الرحلة
    $transportation = transportation::find($transportationId);
    $hotel = hotel::find($hotelId);
    $tripSchadual = trip_schadual::find($tripSchadualId);

    // التواريخ المستخدمة للتحقق من صلاحية التعديل
    $expiryDate = Carbon::parse($optionalJourney->expiry_Date);
    $currentDate = Carbon::now();
    $flightDate = Carbon::parse($optionalJourney->fly_date);
    $bookingDate = Carbon::parse($optionalReservation->created_at);

    // تحديد الفترة المسموح بها للتعديل
    $twoDaysAfterBookingDate = $bookingDate->copy()->addDays(2);
    $fourDaysBeforeFlightDate = $flightDate->copy()->subDays(4);

    // تحقق من أن التعديل يقع ضمن الفترة المسموح بها
    if ($currentDate->lt($expiryDate) &&
        $currentDate->between($bookingDate, $twoDaysAfterBookingDate) &&
        $currentDate->lt($fourDaysBeforeFlightDate)) {

        if ($request->has('Number_of_Tickets')) {
            // حساب الفرق في عدد التذاكر
            $oldNumberOfTickets = (int)$optionalReservation->Number_of_Tickets;
            $newNumberOfTickets = (int)$request->Number_of_Tickets;
            $ticketDifference = $newNumberOfTickets - $oldNumberOfTickets;

            // حساب السعر القديم
            $oldTotalPrice = (float)$hotel->price + (float)$transportation->price + ((float)$optionalJourney->price * $oldNumberOfTickets) + (float)$tripSchadual->price;

            // تحديث قيمة الأماكن المتاحة وحفظها
            if ($ticketDifference > 0) {
                $newAvailableSeats = (int)$optionalJourney->available_seats - $ticketDifference;
                if ($newAvailableSeats < 0) {
                    return response()->json([
                        'message' => 'Requested tickets exceed available seats for this trip. Available tickets: ' . $optionalJourney->available_seats
                    ], 201);
                }
            } else {
                $newAvailableSeats = (int)$optionalJourney->available_seats + abs($ticketDifference);
            }

            $optionalJourney->available_seats = $newAvailableSeats;
            if ($newAvailableSeats === 0) {
                $optionalJourney->available_seats = 'sold out';
            }
            $optionalJourney->save();

            // حساب السعر الجديد
            $newTotalPrice = (float)$hotel->price + (float)$transportation->price + ((float)$optionalJourney->price * $newNumberOfTickets) + (float)$tripSchadual->price;

            // حفظ التعديلات في الحجز
            $optionalReservation->Number_of_Tickets = $newNumberOfTickets;
            $optionalReservation->totalPrice = $newTotalPrice;
            $optionalReservation->save();

            // رسالة نجاح التعديل
            return response()->json([
                'message' => 'Reservation updated successfully.',
                'optional_reservation' => $optionalReservation,
                'old_total_price' => $oldTotalPrice,
                'new_total_price' => $newTotalPrice
            ], 200);
        } else {
            // حالة عدم تعديل عدد التذاكر
            // حساب السعر القديم
            $oldNumberOfTickets = (int)$optionalReservation->Number_of_Tickets;
            $oldTotalPrice = (float)$hotel->price + (float)$transportation->price + ((float)$optionalJourney->price * $oldNumberOfTickets) + (float)$tripSchadual->price;

            // حفظ التعديلات في الحجز
            $optionalReservation->save();

            // رسالة نجاح التعديل بدون تغيير عدد التذاكر
            return response()->json([
                'message' => 'Reservation updated successfully ',
                'optional_reservation' => $optionalReservation,
                'old_total_price' => $oldTotalPrice,
                'new_total_price' => $oldTotalPrice // يتم استخدام السعر القديم هنا لعدم تغييره
            ], 203);
        }
    } else {
        // رسالة خطأ في حالة عدم تحقق الشروط
        return response()->json(['message' => 'Cannot edit reservation, it must be within two days of booking and not within the last four days before the flight.'], 400);
    }
}



public function editConstTripReservation(Request $request, $constTripReservationId, $userId)
{
    // محاولة جلب حجز الرحلة الدائمة
    $constTripReservation = const_trip_reservation::find($constTripReservationId);

    // التحقق من وجود الحجز
    if (!$constTripReservation) {
        return response()->json(['message' => 'Constant trip reservation not found.'], 404);
    }

    // التحقق من صلاحية المستخدم
    if ($userId != $constTripReservation->user_id) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    // جلب بيانات الرحلة الدائمة
    $constTrip = const_trip::find($constTripReservation->constTrip_id);

    if (!$constTrip) {
        return response()->json(['message' => 'Constant trip not found.'], 404);
    }

    // التحقق من توفر الأماكن
    if ($constTrip->available_seats === 0 || $constTrip->available_seats === 'sold out') {
        return response()->json(['message' => 'Tickets for this trip are not available. We will update the tickets soon.'], 400);
    }

    // جلب التواريخ المستخدمة للتحقق من صلاحية التعديل
    $flightDate = Carbon::parse($constTrip->fly_date);
    $expiryDate = Carbon::parse($constTrip->expiry_Date);
    $currentDate = Carbon::now();
    $bookingDate = Carbon::parse($constTripReservation->created_at);

    // التحقق من أن الرحلة لم تنتهِ صلاحيتها
    if ($currentDate->gte($expiryDate)) {
        return response()->json(['message' => 'Cannot edit reservation, trip has expired.'], 401);
    }

    // تحديد الفترة المسموح بها للتعديل
    $twoDaysAfterBookingDate = $bookingDate->copy()->addDays(2);
    $fourDaysBeforeFlightDate = $flightDate->copy()->subDays(4);

    // تحقق من أن التعديل يقع ضمن الفترة المسموح بها
    if (!($currentDate->between($bookingDate, $twoDaysAfterBookingDate) && $currentDate->lt($fourDaysBeforeFlightDate))) {
        return response()->json(['message' => 'Cannot edit reservation, it must be within two days of booking and not within the last four days before the flight.'], 402);
    }

    // التحقق من التذاكر المدخلة إذا كانت موجودة
    if ($request->has('Number_of_Tickets')) {
        $request->validate([
            'Number_of_Tickets' => 'required|numeric|min:1',
        ]);

        // حساب الفرق في عدد التذاكر
        $oldNumberOfTickets = (int)$constTripReservation->Number_of_Tickets;
        $newNumberOfTickets = (int)$request->Number_of_Tickets;
        $ticketDifference = $newNumberOfTickets - $oldNumberOfTickets;

        // حساب السعر القديم
        $oldTotalPrice = (float)$constTripReservation->totalPrice;

        // تحديث قيمة الأماكن المتاحة وحفظها
        if ($ticketDifference > 0) {
            if ($ticketDifference > $constTrip->available_seats) {
                return response()->json(['message' => 'Requested tickets exceed available seats for this trip. Available tickets: ' . $constTrip->available_seats], 201);
            } else {
                $constTrip->available_seats -= $ticketDifference;
            }
        } else {
            $constTrip->available_seats += abs($ticketDifference);
        }

        if ($constTrip->available_seats === 0) {
            $constTrip->available_seats = 'sold out';
        }
        $constTrip->save();

        // حساب السعر الجديد
        $newTotalPrice = (float)$constTrip->Total_Price * $newNumberOfTickets;

        // حفظ التعديلات في الحجز
        $constTripReservation->Number_of_Tickets = $newNumberOfTickets;
        $constTripReservation->totalPrice = $newTotalPrice;
        $constTripReservation->save();

        // رسالة نجاح التعديل
        return response()->json([
            'message' => 'Reservation updated successfully.',
            'constTripReservation' => $constTripReservation,
            'Old Total price' => isset($oldTotalPrice) ? $oldTotalPrice : null,
            'New Total price' => isset($newTotalPrice) ? $newTotalPrice : null,
        ], 200);
    } else {
        // حالة عدم تعديل عدد التذاكر
        // حساب السعر القديم
        $oldTotalPrice = (float)$constTripReservation->totalPrice;

        // حفظ التعديلات في الحجز
        $constTripReservation->save();

        // رسالة نجاح التعديل بدون تغيير عدد التذاكر
        return response()->json([
            'message' => 'Reservation updated successfully ',
            'constTripReservation' => $constTripReservation,
            'Old Total price' => isset($oldTotalPrice) ? $oldTotalPrice : null,
            'New Total price' => isset($oldTotalPrice) ? $oldTotalPrice : null, // يتم استخدام السعر القديم هنا لعدم تغييره
        ], 203);
    }
}

public function EditTicketReservation(Request $request, $userId, $reservationId, $transportationId)
{
    // Validate the input data
    $request->validate([
        'Number_of_Tickets' => 'numeric|min:1',
    ]);

    // Retrieve user, ticket reservation, and transportation
    $user = User::find($userId);
    $ticketReservation = ticket_reservation::find($reservationId);
    $transportation = transportation::find($transportationId);

    // Check if reservation exists and belongs to the user
    if (!$ticketReservation || $ticketReservation->user_id != $userId) {
        return response()->json(['message' => 'Reservation not found or unauthorized access'], 404);
    }

    // Update the transportation ID in the reservation
    $ticketReservation->transportaion_id = $transportation->id;

    // Retrieve ticket data
    $ticket = Ticket::find($ticketReservation->ticket_id);
    if (!$ticket) {
        return response()->json(['message' => 'Ticket not found'], 404);
    }

    if ($ticket->available_seats === 'sold out') {
        return response()->json(['message' => 'The trip is sold out. New trips will be added soon! Stay tuned.'], 202);
    }

    // Dates used for checking the validity of the modification
    $flightDate = Carbon::parse($ticket->fly_date);
    $bookingDate = Carbon::parse($ticketReservation->created_at);
    $expiryDate = Carbon::parse($ticket->expiry_Date);
    $currentDate = Carbon::now();

    // Define the allowed period for modification
    $twoDaysAfterBookingDate = $bookingDate->copy()->addDays(2);
    $fourDaysBeforeFlightDate = $flightDate->copy()->subDays(4);

    // Check that the modification is within the allowed period
    if ($currentDate->lt($expiryDate) &&
        $currentDate->between($bookingDate, $twoDaysAfterBookingDate) &&
        $currentDate->lt($fourDaysBeforeFlightDate)) {

        // Calculate the difference in the number of tickets
        $oldNumberOfTickets = (int)$ticketReservation->Number_of_Tickets;
        $newNumberOfTickets = (int)$request->Number_of_Tickets;
        $ticketDifference = $newNumberOfTickets - $oldNumberOfTickets;

        // Calculate the old total price (including transportation)
        $oldTotalPrice = ($ticket->price * $oldNumberOfTickets) + $transportation->price;

        // Update the available seats and save
        if ($ticketDifference > 0) {
            $newAvailableSeats = (int)$ticket->available_seats - $ticketDifference;
            if ($newAvailableSeats < 0) {
                return response()->json([
                    'message' => 'Requested tickets exceed available seats for this trip. Available tickets: ' . $ticket->available_seats
                ], 201);
            }
        } else {
            $newAvailableSeats = (int)$ticket->available_seats + abs($ticketDifference);
        }

        $ticket->available_seats = $newAvailableSeats;
        if ($newAvailableSeats === 0) {
            $ticket->available_seats = 'sold out';
        }
        $ticket->save();

        // Calculate the new total price (including transportation)
        $newTotalPrice = ($ticket->price * $newNumberOfTickets) + $transportation->price;

        // Save the modifications in the reservation
        $ticketReservation->Number_of_Tickets = $newNumberOfTickets;
        $ticketReservation->totalPrice = $newTotalPrice;
        $ticketReservation->save();

        // Success message for the modification
        return response()->json([
            'message' => 'Reservation updated successfully.',
            'ticket_reservation' => $ticketReservation,
            'old_total_price' => $oldTotalPrice,
            'new_total_price' => $newTotalPrice
        ], 200);
    } else {
        // Error message if conditions are not met
        return response()->json(['message' => 'Cannot edit reservation, it must be within two days of booking and not within the last four days before the flight.'], 400);
    }
}

public function paymentEditingForOptional($oldTotalPrice, $newTotalPrice, $editingId, Request $request)
{
    $editing = optionaljournyReservation::find($editingId);

    // Check if editing is null
    if (!$editing) {
        return response()->json(['error' => 'Reservation not found!'], 404);
    }

    $user = $editing->user_id;
    $findUser = User::find($user);

    // Check if user is null
    if (!$findUser) {
        return response()->json(['error' => 'User not found!'], 404);
    }

    if ($request->input('payment_status') == 'From Wallet') {
        if ($newTotalPrice > $oldTotalPrice) {
            $diff = $newTotalPrice - $oldTotalPrice;
            if ($findUser->wallet > $diff) {
                $findUser->wallet -= $diff;
                $editing->payment_status = 'edited(paid From Wallet)';
                $findUser->save();
                $editing->save();
                return response()->json([
                    'message' => 'Payment updated successfully!',
                    'your wallet has :' => $findUser->wallet
                ], 200);
            } else {
                return response()->json(['error' => 'You do not have enough money in your wallet, please charge!'], 400);
            }
        } elseif ($oldTotalPrice > $newTotalPrice) {
            $diff = $oldTotalPrice - $newTotalPrice;
            $findUser->wallet += $diff;
            $editing->payment_status = 'edited(paid From Wallet)';
            $findUser->save();
            $editing->save();
            return response()->json([
                'message' => 'Payment updated successfully!',
                'we returned the following amount to your wallet:' => $diff,
                'your wallet has :' => $findUser->wallet
            ], 200);
        }
    } elseif ($request->input('payment_status') == 'manual') {
        return response()->json([
            'message' => 'You have to pay within five days or else we will delete your reservation'
        ], 202);
    }

    return response()->json(['error' => 'Invalid payment status!'], 400);
}

public function paymentEditingForConst($oldTotalPrice,$newTotalPrice,$editingId,Request $request){

    $editing=const_trip_reservation::find($editingId);
    $user=$editing->user_id;
    $findUser=User::find($user);
    if($request->input('payment_status') == 'From Wallet'){
        if($newTotalPrice>$oldTotalPrice){
            $diff=$newTotalPrice-$oldTotalPrice;
            if($findUser->wallet>$diff){
                $findUser->wallet-=$diff;
                $editing->payment_status='edited(paid From Wallet)';
                return response()->json(['payment updated successfully!',
                'your wallet has :'=> $findUser->wallet
            ],200);
            }
            else{
                return response()->json(['you do not have enough mony in your wallet!,please charge!'],201);
            }
        }
        elseif($oldTotalPrice>$newTotalPrice){
            $diff=$oldTotalPrice-$newTotalPrice;
            $findUser->wallet+=$diff;
            $editing->payment_status='edited(paid From Wallet)';
            return response()->json(['payment updated successfully!',
            'we returned the following amount to your wallet:'=>$diff,
            'your wallet has :'=> $findUser->wallet
        ],201);
        }
    }
    elseif($request->input('payment_status') == 'manual'){
    return response()->json(['you have to pay until five days else we will delete your reservation'
    ],202);




    }
}
public function paymentEditingForTicket($oldTotalPrice, $newTotalPrice, $editingId, Request $request)
{
    $editing = ticket_reservation::find($editingId);

    if (!$editing) {
        return response()->json(['message' => 'Reservation not found'], 404);
    }

    $user = $editing->user_id;
    $findUser = User::find($user);

    if (!$findUser) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($request->input('payment_status') == 'From Wallet') {
        if ($newTotalPrice > $oldTotalPrice) {
            $diff = $newTotalPrice - $oldTotalPrice;
            if ($findUser->wallet > $diff) {
                $findUser->wallet -= $diff;
                $editing->payment_status = 'edited(paid From Wallet)';
                $editing->save();
                $findUser->save();
                return response()->json([
                    'message' => 'Payment updated successfully!',
                    'your wallet has' => $findUser->wallet
                ], 200);
            } else {
                return response()->json(['message' => 'You do not have enough money in your wallet! Please charge!'], 400);
            }
        } elseif ($oldTotalPrice > $newTotalPrice) {
            $diff = $oldTotalPrice - $newTotalPrice;
            $findUser->wallet += $diff;
            $editing->payment_status = 'edited(paid From Wallet)';
            $editing->save();
            $findUser->save();
            return response()->json([
                'message' => 'Payment updated successfully!',
                'we returned the following amount to your wallet' => $diff,
                'your wallet has' => $findUser->wallet
            ], 200);
        }
    } elseif ($request->input('payment_status') == 'manual') {
        return response()->json(['message' => 'You have to pay within five days, else we will delete your reservation'], 202);
    }

    return response()->json(['message' => 'Invalid payment status'], 400);
}


}
