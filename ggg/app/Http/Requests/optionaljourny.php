<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class optionaljourny extends FormRequest
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
            'destination_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'destination_ar'=> 'required|regex:/^[\p{Arabic}\s]+$/u',
            'expiry_Date'=>'required|date|after_or_equal:today',
              'price'=>'required|numeric|min:1000',
              'available_seats'=>'required|numeric|min:1',
              'season_id'=>'required',
              'section_id'=>'required',
              'continents_id'=>'required',
              'type_ticket_id'=>'required',
              'journy_photo1'=>'required',
              'journy_photo2'=>'required',
              'journy_photo3'=>'required',
              'fly_date'=>'required|date_format:Y-m-d',
              'fly_time' => 'required|date_format:"g:i A"',
              'Number_of_flight_hours'=>'required|numeric|min:3'

          ];
      }
      public function messages(){
          return [
          'destination.required'=>'please enter destination',
          //'expiry_Date.required'=>'please enter expiry Date',
          'price.required'=>'please enter the price of the journy',
          'available_seats.required'=>'enter the number of available places',
          'photo1.required'=>'please enter photo for this journy',
          'photo2.required'=>'please enter photo for this journy',
          'photo3.required'=>'please enter photo for this journy'


          ];
      }
}
