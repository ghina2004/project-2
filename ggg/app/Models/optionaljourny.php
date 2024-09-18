<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class optionaljourny extends Model
{
    use HasFactory;
    protected $fillable= ['destination','expiry_Date','fly_date','fly_time','Number_of_flight_hours','price','available_seats','season_id','section_id','type_ticket_id','continents_id','journy_photo1','journy_photo2','journy_photo3'];
    public function optionalreservation(){
        return $this->hasMany(optionaljournyReservation::class);
      }
    public function cont(){
       return $this->BelongsTo(continent::class);
    }
    public function seas(){
       return $this->belongsTo(season::class);
    }
    public function sec(){
       return $this->belongsTo(section::class);
    }
}
