<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ticket extends FormRequest
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
            'destination_en'=>'required||regex:/^[a-zA-Z ]+$/',
            'destination_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'expiry_Date'=>'required|date|after_or_equal:today',
            'fly_date'=>'required|date_format:Y-m-d',
            'fly_time' => 'required|date_format:"g:i A"',
            'Number_of_flight_hours'=>'required|numeric|min:3',
            'price'=>'required|numeric|min:1000',
            'available_seats'=>'required|numeric|min:1',
            'continents_id'=>'required',
            'journy_photo1'=>'required',
            'journy_photo2'=>'required',
            'journy_photo3'=>'required',
            'type_ticket_id'=>'required'

        ];
    }
}
