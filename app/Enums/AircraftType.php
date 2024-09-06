<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AircraftType: string implements HasColor, HasIcon, HasLabel
{
    case Ballon = '0';
    case Airplane = '1';
    case Rocket = '2';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ballon => 'Hőlégballon',
            self::Airplane => 'Kisrepülőgép',
            self::Rocket => 'Űrrakéta',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Ballon => 'gray',
            self::Airplane => 'gray',
            self::Rocket => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Ballon => 'iconoir-hot-air-balloon',
            self::Airplane => 'iconoir-airplane',
            self::Rocket => 'iconoir-rocket',
        };
    }
}
