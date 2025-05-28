<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'locations';

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
