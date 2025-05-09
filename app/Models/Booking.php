<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    protected $fillable = [
        'user_id',
        'pickup_address',
        'drop_address',
        'package_id',
        'distance_km',
        'duration_hr',
        'extra_km',
        'extra_hr',
        'final_price',
    ];

    // booking  to a package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // booking to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
