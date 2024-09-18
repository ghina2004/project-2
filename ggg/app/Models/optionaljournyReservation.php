<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class optionaljournyReservation extends Model
{
    use HasFactory;
    protected $guarded = [];

public function hotel()
{
    return $this->belongsTo(Hotel::class); }
    
    public function optional_journy(){
        return $this->BelongsTo(optionaljourny::class);
    }
}
