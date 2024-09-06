<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketTypePrivate: string implements HasColor, HasIcon, HasLabel
{
    case PrivateFalse = '0';
    case PrivateTrue = '1';

    public function getLabel(): string
    {
        return match ($this) {
            self::PrivateFalse => false,
            self::PrivateTrue => 'PRIVÁT',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PrivateFalse => 'gray',
            self::PrivateTrue => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PrivateFalse => 'tabler-crown',
            self::PrivateTrue => 'tabler-crown',
        };
    }
}
