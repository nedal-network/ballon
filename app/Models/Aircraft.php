<?php

namespace App\Models;

use App\Enums\AircraftType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aircraft extends Model
{
    protected $casts = [
        'type' => AircraftType::class,
    ];

    use HasFactory;
    
    use SoftDeletes;

    public function tickettypes(): BelongsToMany
    {
        return $this->belongsToMany(Tickettype::class, 'aircraft_tickettype');
    }

    //légijármű selector szabályrendszer
    public static function flyable($passenger_count, $vip_flag, $private_flag, $aircraft_type)
    {
        return self::where(function ($q) use ($passenger_count) {
            $q->where('unlimited', '=', 1)->orWhere('number_of_person', '>=', $passenger_count);
        })->where('vip', '=', $vip_flag)->where('private', '=', $private_flag)->where('type', $aircraft_type)->get();
    }
}