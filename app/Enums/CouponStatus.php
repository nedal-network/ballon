<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CouponStatus: int implements HasColor, HasIcon, HasLabel
{
    case UnderProcess = 0; //feldolgozás alatt
    case CanBeUsed = 1; //felhasználható
    case Used = 3; //felhasznált
    case Expired = 4; //lejárt
    case Applicant = 5; //jelentkezett

    public function getLabel(): string
    {
        return match ($this) {
            self::UnderProcess => 'Feldolgozás alatt',
            self::CanBeUsed => 'Felhasználható',
            self::Used => 'Felhasznált',
            self::Expired => 'Lejárt',
            self::Applicant => 'Jelentkezett',
        };
    }

    public function getSelectLabel(): string
    {
        return match ($this) {
            self::UnderProcess => 'Jóváhagyásra vár',
            self::CanBeUsed => 'Jóváhagyva',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UnderProcess => 'warning',
            self::CanBeUsed => 'success',
            self::Used => 'danger',
            self::Expired => 'warning',
            self::Applicant => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::UnderProcess => 'tabler-progress-check',
            self::CanBeUsed => 'tabler-discount-check',
            self::Used => 'tabler-circle-x',
            self::Expired => 'tabler-soup',
            self::Applicant => 'tabler-plane-departure',
        };
    }
}
