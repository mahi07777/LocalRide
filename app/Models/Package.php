<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    //
    protected $fillable = [ 
        'name',
        'max_km',
        'max_hours',
        'base_price',
        'extra_km_price',
        'extra_hr_price',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
