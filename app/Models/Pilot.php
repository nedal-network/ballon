<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pilot extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function fullname(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->lastname.' '.$this->firstname,
        );
    }

    use SoftDeletes;
}
