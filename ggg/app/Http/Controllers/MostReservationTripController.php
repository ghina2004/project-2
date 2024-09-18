<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MostReservationTripController extends Controller
{
    public function getMostReservationTrip()
    {
        $results = [];
        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;


        do {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $most_trip = const_trip::query()->withCount('constTripReservations')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->orderBy('const_trip_reservations_count', 'desc')
                ->first();

            if ($most_trip) {
                $results[] = [
                    'month' => $monthStart->format('Y-m'),
                    'data' => $most_trip,
                ];
            } else {
                $results[] = [
                    'month' => $monthStart->format('Y-m'),
                    'message' => 'No reservations for this month',
                ];
            }

            $currentDate->subMonth();
        } while ($currentDate->month != 1 || $currentDate->year != $currentYear);


        $monthStart = $currentDate->copy()->startOfMonth();
        $monthEnd = $currentDate->copy()->endOfMonth();
        $most_trip = const_trip::query()->withCount('constTripReservations')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->orderBy('const_trip_reservations_count', 'desc')
            ->first();
        if ($most_trip) {
            $results[] = [
                'month' => $monthStart->format('Y-m'),
                'data' => $most_trip,
            ];
        } else {
            $results[] = [
                'month' => $monthStart->format('Y-m'),
                'message' => 'No reservations for this month',
            ];
        }

        return response()->json([
            'status' => '1',
            'data' => $results,
            'message' => trans('successfuly'),
        ], 200);
    }

}

