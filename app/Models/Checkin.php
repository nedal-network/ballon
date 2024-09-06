<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    // NE töröld ezt a Model-t!
    // Ezt a Model-t használjuk az `App\Filament\Pages\Checkin.php`-ben a `checkIn` és `checkOut` eljárásoknál;

    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;
}
