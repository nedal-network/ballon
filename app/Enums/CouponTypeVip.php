<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CouponTypeVip: string implements HasColor, HasIcon, HasLabel
{
    case VipFalse = '0';
    case VipTrue = '1';

    public function getLabel(): string
    {
        return match ($this) {
            self::VipFalse => false,
            self::VipTrue => 'VIP',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::VipFalse => 'gray',
            self::VipTrue => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::VipFalse => 'tabler-vip',
            self::VipTrue => 'tabler-vip',
        };
    }
}
