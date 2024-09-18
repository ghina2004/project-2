<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class transportation extends FormRequest
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
            'transportation_Name_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'transportation_Name_ar' =>'required|regex:/^[\p{Arabic}\s]+$/u',
            'price'=>'required|numeric|min:100',
            'country_Name_en'=>'required|regex:/^[a-zA-Z ]+$/',
            'country_Name_ar'=>'required|regex:/^[\p{Arabic}\s]+$/u',
            'photo1'=>'required',
            'photo2'=>'required',
            'photo3'=>'required'
        ];
    }
    public function message(){
      return [
'transportation_Name.required'=>'you have to enter available transporation',
'price.required'=>'you have to enter available price',
'price.numeric'=>'you have to enter numbers',
'price.min:100'=>'you have to enter available price,EX:at lest 100'
      ]  ;
    }
}
