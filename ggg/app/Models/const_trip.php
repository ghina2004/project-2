<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class const_trip extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function constTripReservations()
    {
        return $this->hasMany(const_trip_reservation::class, 'constTrip_id');
    }

    public function ratings(){
        return $this->hasMany(Rating::class);

    }



    public function comments(){
        return $this->hasMany(Comment::class);

    }
}
