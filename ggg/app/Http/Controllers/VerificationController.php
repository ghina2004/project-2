<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify($id,Request$request){
        if(!$request->hasValidSignature()){
            return response()->json([
               'message'=> __('Invalid Url')
            ],401);
        }
        $user =User::query()->findOrFail($id);
        if(!$user->hasverifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return response()->json([

            'message'=>__('Email Verified Successfully')
        ]);
    }


    public function resend(){
        if(auth()->user()->hasVerifiedEmail()){
            return response()->json([
                'message'=>trans('Email  already verified ')
            ],400);
        }
        else {
           auth()->user()->sendEmailVerificationNotification();
        return response()->json([
            'message'=> __('Email verification link sent on your email ')
        ]);}
    }





}
