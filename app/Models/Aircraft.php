<?php

namespace App\Models;

use App\Enums\AircraftType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aircraft extends Model
{
    protected $guarded = [];

    protected $casts = [
        'type' => AircraftType::class,
    ];

    use HasFactory;
    use SoftDeletes;

    public function tickettypes(): BelongsToMany
    {
        return $this->belongsToMany(Tickettype::class, 'aircraft_tickettype');
    }

    public function events()
    {
        return $this->hasMany(AircraftLocationPilot::class, 'aircraft_id', 'id');
    }

    //légijármű selector szabályrendszer
    public static function flyable($passenger_count, $tickettype_id)
    {
        return self::Where('number_of_person', '>=', $passenger_count)->where('type', $tickettype_id)->get();
    }
}
