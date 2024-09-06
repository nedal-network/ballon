<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaType extends Model
{
    protected $guarded = [];

    //use HasFactory;
    public function locations()
    {
        return $this->hasMany(Location::class); // itt azt definiáltuk, hogy egy cikkhez több azaz 'hasMany' comment is tartozhat.
    }

    public function flightlocations()
    {
        return $this->hasMany(Flightlocation::class); // itt azt definiáltuk, hogy egy cikkhez több azaz 'hasMany' comment is tartozhat.
    }
}
