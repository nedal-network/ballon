<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    //use HasFactory;
    protected $guarded = [];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    use SoftDeletes;
}
