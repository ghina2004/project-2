<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\const_trip;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index($id)
    {
        $trip=const_trip::query()->find($id);

        $comments = $trip->comments()->get();

        if($comments){
            return response()->json([
                'status' => '1',
                'message' =>__( 'Indexed successfully'),
                'data' => $comments
            ], 200);
        }
         else{
        return response()->json([
            'status' => '1',
            'message' => __('No comments'),
            'data' => []
        ], 200);
    }}

    public function store(Request $request){
        $trip = const_trip::find($request->const_trip_id);
        if (!$trip)
            return response()->json([
                'message' => __('not found')
            ], 404);

     $request->validate([
         'value'=>['required','string','min:1','max:400'] ,
     ]) ;
     $dd=comment::query()->create([
         'value'=>$request['value'],
         'const_trip_id'=>$trip['id'],
         'user_id'=>\auth()->id()
     ]);
        return response()->json([
            'status' => '1',
            'message' => __('Commented successfully'),
            'data' => $dd
        ], 200);
    }

    public function show($id)
    {
        $comment = Comment::find($id);

        return response()->json([
            'status' => '1',
            'message' => __('Showed successfully'),
            'data' => $comment
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'value'=>['required','string','min:1','max:400'] ,
        ]);
        $comment = Comment::query()->find($id);

        $ff=$comment->update([
            'value' =>$request['value']
        ]);

        return response()->json([
            'status' => '1',
            'message' => __('Updated successfully'),
            'data' => $ff
        ], 200);
    }

    public function delete($id)
    {
        $comment = Comment::query()->find($id);

        if ($comment) {
            $comment->delete();

            return response()->json([
                'status' => '1',
                'message' => __('Deleted successfuly'),
            ], 200);
        } else {
            return response()->json([
                'status' => '0',
                'message' => __('Invalid ID'),
            ], 404);
        }}
}
