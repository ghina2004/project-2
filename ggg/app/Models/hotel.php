<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hotel extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function journieshotel(){
        return $this->hasMany(optionaljourny::class);
    }

    public function restaurants(){
        return $this->hasone(Restaurant::class);
    }

    public function consttrip(){
        return $this->hasMany(const_trip::class);
    }
    public function optionalJournyReservation(){
        return $this->hasone(optionaljournyReservation::class);
    }

}
