<?php

namespace App\Models;

use App\Enums\AircraftType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tickettype extends Model
{
    protected $guarded = [];

    protected $casts = [
        'aircrafttype' => AircraftType::class,
    ];

    //use HasFactory;
    /*
    public function aircrafts()
    {
        return $this->hasMany(Aircraft::class); // itt azt definiáltuk, hogy egy jegytípushoz több légijármű is tartozhat.
    }
    */
    public function aircrafts(): BelongsToMany
    {
        return $this->belongsToMany(Aircraft::class, 'aircraft_tickettype');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'tickettype_region');
    }

    use SoftDeletes;
}
