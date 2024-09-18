<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class const_trip_reservation extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function constTrip(){
        return $this->belongsTo(const_trip::class,'constTrip_id');

    }
}
