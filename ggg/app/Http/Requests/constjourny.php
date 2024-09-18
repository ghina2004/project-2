<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class constjourny extends FormRequest
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
            'destination_en' => 'required|regex:/^[a-zA-Z ]+$/',
            'destination_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
                'fly_date' => 'required|date_format:Y-m-d',
                'fly_time' => 'required|date_format:"g:i A"',
                'expiry_Date'=>'required|date_format:Y-m-d|after_or_equal:today',
                'Number_of_flight_hours' => 'required|numeric|min:2',
                'price' => 'required|numeric|min:10000',
                'available_seats' => 'required|numeric|min:4',
                'season_id' => 'required',
                'section_id' => 'required',
                'continents_id' => 'required',
                'type_ticket_id'=>'required',
                'descripyion_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'descripyion_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'photo1'=>'required',
            'photo2'=>'required',
            'photo3'=>'required',

        ];
    }
}
