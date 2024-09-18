<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class section extends Model
{
    use HasFactory;
    protected $guarded = [];
     public function journies(){
        return $this->hasMany(optionaljourny::class);
    }
    public function typeticket(){
        return $this->hasMany(type_ticket::class);
    }
}
