<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class trip_schadual extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function optionalreservation(){
        return $this->hasMany(optionaljournyReservation::class);
    }
    public function consttrip(){
        return $this->hasMany(const_trip::class);
    }
}
