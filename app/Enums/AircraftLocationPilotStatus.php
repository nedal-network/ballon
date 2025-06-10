<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AircraftLocationPilotStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = '0'; //tervezett
    case Published = '1'; //publikált
    case Finalized = '2'; //véglegesített
    case Executed = '3'; //végrehajtott
    case Deleted = '4'; //törölt
    case Feedback = '5'; //visszajelzés

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Tervezett',
            self::Published => 'Publikált',
            self::Finalized => 'Véglegesített',
            self::Executed => 'Végrehajtott',
            self::Deleted => 'Törölt',
            self::Feedback => 'Visszajelzés',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Published => 'success',
            self::Finalized => 'info',
            self::Executed => 'info',
            self::Deleted => 'danger',
            self::Feedback => Color::rgb('rgb(193,154,107)'), // brown
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'tabler-player-pause',
            self::Published => 'tabler-player-play',
            self::Finalized => 'tabler-flag-check',
            self::Executed => 'tabler-player-stop',
            self::Deleted => 'tabler-playstation-x',
            self::Feedback => 'tabler-mail-heart',
        };
    }
}
