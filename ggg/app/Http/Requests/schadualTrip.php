<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class schadualTrip extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
           'firstDayPlace1_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'firstDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'firstDayPlace2_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'firstDayPlace2_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'firstDayPlace3_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'firstDayPlace3_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'secondDayPlace1_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'secondDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'secondDayPlace2_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'secondDayPlace2_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'secondDayPlace3_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'secondDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'ThirdDayPlace1_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'ThirdDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'ThirdDayPlace2_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'ThirdDayPlace2_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'ThirdDayPlace3_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'ThirdDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FourthDayPlace1_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'FourthDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'FourthDayPlace2_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'FourthDayPlace2_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
           'FourthDayPlace3_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'FourthDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
           'FifthDayPlace1_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'FifthDayPlace1_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
           'FifthDayPlace2_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'FifthDayPlace2_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
           'FifthDayPlace3_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'FifthDayPlace3_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'time1' => 'required|date_format:"g:i A"',
            'time2' => 'required|date_format:"g:i A"',
            'time3' => 'required|date_format:"g:i A"',
            'time4' => 'required|date_format:"g:i A"',
            'time5' => 'required|date_format:"g:i A"',
            'time6' => 'required|date_format:"g:i A"',
            'time7' => 'required|date_format:"g:i A"',
            'time8' => 'required|date_format:"g:i A"',
            'time9' => 'required|date_format:"g:i A"',
            'time10' => 'required|date_format:"g:i A"',
            'time11' => 'required|date_format:"g:i A"',
            'time12' => 'required|date_format:"g:i A"',
            'time13' => 'required|date_format:"g:i A"',
            'time14' => 'required|date_format:"g:i A"',
            'time15' => 'required|date_format:"g:i A"',
            'destination_en' => 'regex:/^[a-zA-Z ]+$/',
            'destination_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
        'fly_date' => 'date_format:Y-m-d',
        'fly_time' => 'date_format:"g:i A"',
        'expiry_Date' => 'date_format:Y-m-d|after_or_equal:today',
        'Number_of_flight_hours' => 'numeric|min:2',
        'price' => 'numeric|min:10000',
        'available_seats' => 'numeric|min:4',
        
            'priceFor1Day' => 'required|numeric|min:1000',
            'priceFor2Day' => 'required|numeric|min:1000',
            'priceFor3Day' => 'required|numeric|min:1000',
            'priceFor4Day' => 'required|numeric|min:1000',
            'priceFor5Day' => 'required|numeric|min:1000',
        ];







    }
}
