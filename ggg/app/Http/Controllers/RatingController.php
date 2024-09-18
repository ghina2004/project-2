<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\const_trip;
use App\Models\Rating;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function index($id)
    {

        $tt = const_trip::query()->find($id);


        if($tt){
            return response()->json([
                'status' => '1',
                'message' =>trans ('Indexed successfully'),
                'data' => $tt
            ], 200);
        }
        else{
            return response()->json([
                'status' => '1',
                'message' =>trans( 'No Ratings'),
                'data' => []
            ], 200);
        }}


    public function store(Request $request){

        $trip=const_trip::query()->find($request->const_trip_id);

        if (!$trip)
            return response()->json([
                'message' => trans('not found')
            ], 404);

        $request->validate([
            'value'=>['required','integer','min:1','max:5'] ,

        ]) ;
        $dd=Rating::query()->create([
            'value'=>$request['value'],
            'const_trip_id'=>$trip['id'],
            'user_id'=>\auth()->id()
        ]);
             $trip->update([
            'avg'=>$trip->ratings()->avg('value')
        ]);
        return response()->json([
            'status' => '1',
            'message' =>trans( 'Rated successfully'),
            'data' => $dd
        ], 200);
    }

    public function show($id)
    {
        $rr = Rating::find($id);

        return response()->json([
            'status' => '1',
            'message' => trans('Showed successfully'),
            'data' => $rr
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $trip=const_trip::query()->find($request->const_trip_id);

        $request->validate([
            'value'=>['required','integer','min:1','max:5'] ,
        ]);
        $tt = Rating::query()->find($id);

        $ff=$tt->update([
            'value' =>$request['value']
        ]);
        $trip->update([
            'avg'=>$trip->ratings()->avg('value')
        ]);

        return response()->json([
            'status' => '1',
            'message' => __('Updated successfully'),
            'data' => $ff
        ], 200);
    }

    public function delete(Request $request,$id)
    {
        $trip=const_trip::query()->find($request->const_trip_id);

        $rating = Rating::query()->find($id);

        if ($rating) {

            $rating->delete();

            $trip->update([
                'avg'=>$trip->ratings()->avg('value')
            ]);

            return response()->json([
                'status' => '1',
                'message' => trans('Deleted successfuly'),
            ], 200);
        } else {
            return response()->json([
                'status' => '0',
                'message' =>trans( 'Invalid ID'),
            ], 404);
        }}

    public function Highest_rating(){
      $w=  const_trip::query()->orderbydesc('avg')->get();
        return response()->json(
            
           
             $w
        , 200);
}











}
