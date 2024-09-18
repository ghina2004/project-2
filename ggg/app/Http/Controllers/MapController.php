<?php

namespace App\Http\Controllers;

use App\Models\const_trip;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function map(Request $request)
    {

        $long = $request->long;
        $lat = $request->lat;

        $url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$long&format=json";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'status' => 1,
                'data' => $data,
                'message' => __(' success')
            ], 200);
        } else {
            return response()->json([
                'status' => 1,
                'data' => [],
                'message' => __(' failed')
            ], 400);

        }

    }
}
