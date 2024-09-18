<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate( [
            'first_name' => ['string','required','min:3','max:50','regex:/^[\pl\s]+$/u'],
            'middle_name' => ['string','required','min:3','max:50','regex:/^[\pl\s]+$/u'],
            'last_name' => ['string','required','min:3','max:50','regex:/^[\pl\s]+$/u'],
            'email' => ['required','email', 'unique:users,email'],
            'password' => ['required', 'min:8','max:50','confirmed'],
            'phone_number'=>['nullable','phone:AUTO']
        ]);



        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'first_name' => $request['first_name'],
            'middle_name'=>$request['middle_name'],
            'last_name' =>$request['last_name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'phone_number'=> $request['phone_number']

        ]);

        $user->sendEmailVerificationNotification();


       $token = $user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['user'] = $user;
        $data['token'] = $token;
        $message = 'Email verification link sent on your email';
        return response()->json([
            'status' => 1,
            'data' => $data,
            'message' => __($message)
        ],200);
    }

    public function login(Request $request): JsonResponse
    { $request->validate( [
        'email' => ['required','email', 'exists:users,email'],
        'password' => ['required', 'min:8','max:50','confirmed'],
        'role'=>['boolean']

        ]);

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'data' => [],
                'status' => 0,
                'message' => __('Email & password doses not match with our record')
            ], 405);
        }
        $user = User::query()->where('email', '=', $request['email'])->first();
        $token = $user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['user'] = $user;
        $data['token'] = $token;
        return response()->json([
            'status' => 1,
            'data' => $data,
            'message' =>__( 'logged in successfully')
        ],200);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 1,
            'data' => [],
            'message' =>__( 'logged out successfully')
        ],200);
    }


    public function userForgotPassword(Request$request){
        $data =$request->validate([
            'email'=>['required','email','exists:users,email']
        ]);
        ResetCodePassword::query()->where('email',$request['email'])->delete();

        $data ['code']=mt_rand(100000,999999);
        $codeData =ResetCodePassword::query()->create($data);
        Mail::to($request['email'])->send(new SendCodeResetPassword($codeData['code']));
        return response()->json([
            'status'=> 1,
           'data'=> [],
           'message'=> trans('code sent successfully')],200);
    }

    public function userCheckCode(Request$request)
    {
        $request->validate([
            'code' => ['required', 'string', 'exists:reset_code_passwords']]);
        $passwordReset = ResetCodePassword::query()->firstWhere('code', $request['code']);

            if( $passwordReset['created_at']->addDay() < Carbon::now()) {
            return response()->json([
                'status' => 0,
                'data' =>[],
                'message' => trans('password.code is expire')], 422);

                $passwordReset->delete();
            }
else{
    return response()->json([
        'status'=> 1,
        'data'=>[],
        'message'=>trans('password.code is valid')],200);
}

    }

    public function userResetPassword(Request$request){
        $input=$request->validate([
            'code'=>['required','string','exists:reset_code_passwords,code'],
            'password'=>['required', 'min:8','max:50','confirmed']
        ]);

        $passwordReset = ResetCodePassword::query()->firstWhere('code',$request['code']);

        if( $passwordReset['created_at']->addDay() < Carbon:: now()) {
            $passwordReset->delete();
            return response()->json([
                'status' => 0,
                'data' =>[],
                'message'=>trans('password.code is expire')],422);
        }
        $user =User::query()->firstWhere('email',$passwordReset['email']);

        $input['password']=bcrypt($input['password']);

        $user->update([
            'password'=>$input['password']]);

           $passwordReset->delete();
           return response()->json([
               'status' => 1,
               'data' =>$user,
               'message'=>trans('password has been successfully reset') ],200);

    }

    public function deleteAccount()
    {
        $user=Auth::user();
        $user->delete();
        $user['status_money']= 'The money was returned';
        $user->save();
        return response()->json([
            'status' => 1,
            'data' =>[],
            'message'=>trans('deleted account successfully') ],200);


    }

    public function recoveryAccount($id){
        $user = User::withTrashed()->find($id);
        $user->restore();
        $user['wallet'] = 0 ;
        $user->save();
        $token = $user->createToken("API TOKEN")->plainTextToken;
        $data = [];
        $data['user']= $user ;
        $data['token']= $token ;
        return response()->json([
            'status' => 1,
            'data' =>$data,
            'message'=>trans('retrieved account successfully') ],200);



    }
    public function showProfile(){
        $user_id = Auth::id();

        $profile =User::query()->find($user_id);

        return response()->json([
            'status' => 1,
            'data' =>$profile,
            'message'=>trans('showed profile successfully') ],200);

    }
    public function updateProfile(Request$request){
     $request->validate([
         'first_name' =>['string','min:3','max:50','regex:/^[\pl\s]+$/u'],
         'middle_name'=>['string','min:3','max:50','regex:/^[\pl\s]+$/u'],
         'last_name'=>['string','min:3','max:50','regex:/^[\pl\s]+$/u'],
         'image'=> ['image','mimes:jpeg,png,bmp,jpg,gif,svg,,webp,tiff'],
         'phone_number'=>['nullable','phone:Auto'],
     ]);
     $image_name = null ;
     if ($image = $request->file('image')) {
         $image_name = time().'-'.$image->getClientOriginalExtension();
         $image->move(public_path('image'), $image_name);
         $image_name ='image/'. $image_name ;

     }

        $user_id = Auth::id();
      $user= User::query()->find( $user_id);
        $user->update([
           'first_name'=> $request['first_name']?? $user['first_name'],
           'middle_name'=> $request['middle_name']?? $user['middle_name'],
           'last_name'=> $request['last_name']?? $user['last_name'],
            'image'=> isset( $request['image'])? $image_name :  $user['image'],
            'phone_number'=> $request['phone_number']?? $user['phone_number'],
       ]);
        $user= User::query()->find( $user_id);

        return response()->json([
            'status' => 1,
            'data' =>$user,
            'message'=>trans('updated profile successfully') ],200);

    }
    public function deleteImageProfile(){

        $user = Auth::user();

         if($user['image'] ){
             $user['image'] = null ;
             $user->save();

            // Storage::disk('public')->delete($path);

             return response()->json([
                 'status' => '1',
                 'message' => trans('Deleted successfuly'),
             ], 200);

         } else {
             return response()->json([
                 'status' => '0',
                 'message' =>trans( 'no image'),
             ], 404);
         }


    }
    public function updatePassword (Request $request){
        $request->validate([
            'current_password' => ['required'],
            'new_password' =>['required','string','min:8','max:50','confirmed']
        ]);
        $user = Auth::user();

        if (!Hash::check($request['current_password'], $user['password'])) {
            return response()->json([
                'status' => '0',
                'message' =>trans( 'current password incorrect'),
            ], 400);
        }
        else{
            $user['password'] = Hash::make($request['new_password']);
            $user->save();
            return response()->json([
                'status' => '1',
                'data' => $user,
                'message' =>trans( 'update password successfully'),
            ], 200);
        }}


    public function getAllUser(){
        $user =User::query()->get();

        return response()->json([
            'status' => '1',
            'data' => $user,
            'message' =>trans( 'showed successfully'),
        ], 200);


    }

}































