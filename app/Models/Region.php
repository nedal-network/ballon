<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Region extends Model
{
    protected $guarded = [];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function tickettypes(): BelongsToMany
    {
        return $this->belongsToMany(Tickettype::class, 'tickettype_region');
    }
}
