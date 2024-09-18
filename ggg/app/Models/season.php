<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class season extends Model
{
    use HasFactory;
    protected $guarded = [];

public function journy2(){
    return $this->hasMany(optionaljourny::class);
}
}
