<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Resquesthotel extends FormRequest
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
            'country_Name_en' => 'required|regex:/^[a-zA-Z ]+$/',
            'hotel_Name_en' => 'required|regex:/^[a-zA-Z ]+$/',
            'price' => 'required|numeric|min:100',
            'Type_Reservation_en' => 'required|regex:/^[a-zA-Z ]+$/',
            'photo1' => 'required',
            'photo2' => 'required',
            'photo3' => 'required',
            'country_Name_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
            'hotel_Name_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
            'Type_Reservation_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
            'description_ar' => 'required|regex:/^[\p{Arabic}\s]+$/u',
            'description_en' => 'required|regex:/^[a-zA-Z ]+$/',
        ];
    }
}
