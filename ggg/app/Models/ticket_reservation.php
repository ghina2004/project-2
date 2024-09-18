<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket_reservation extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function Ticket (){
        return $this->belongsTo(ticket::class);

    }
}
