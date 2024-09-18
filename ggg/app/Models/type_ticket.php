<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class type_ticket extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function optionaljourny(){
        return $this->hasMany(optionaljourny::class);
    }
    public function consttrip(){
        return $this->hasMany(const_trip::class);
    }
    public function ticket(){
        return $this->hasMany(ticket::class);
    }
    
}
