<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transportation extends Model
{
    use HasFactory;
    protected $guarded = [];

public function constTrip(){
    return $this->hasMany(const_trip::class);
}
public function optionalJournyReservation(){
    return $this->hasone(optionaljournyReservation::class);
}
public function ticket(){
    return $this ->hasMany(ticket_reservation::class);
}
}
