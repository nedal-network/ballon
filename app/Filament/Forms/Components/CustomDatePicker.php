<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\DatePicker;

class CustomDatePicker extends DatePicker
{
    /**
     * @var view-string
     */
    protected string $view = 'filament.forms.date-time-picker';

    protected string|Closure $mask = '9999.99.99.';

    protected string|Closure $pattern = '(19|20)\d{2}\.(0[1-9]|1[0,1,2])\.(0[1-9]|[12][0-9]|3[01])\.'; // 1900-2999.01-12.01-31.

    public static string $defaultDateDisplayFormat = 'Y.m.d.';

    public static string $defaultDateTimeDisplayFormat = 'Y.m.d. H:i';

    public static string $defaultDateTimeWithSecondsDisplayFormat = 'Y.m.d. H:i:s';

    public static string $defaultTimeDisplayFormat = 'H:i';

    public static string $defaultTimeWithSecondsDisplayFormat = 'H:i:s';

    public function getPlaceholder(): ?string
    {
        return $this->evaluate($this->placeholder) ?? now()->format($this->getDisplayFormat());
    }

    public function pattern(string|Closure $pattern): static
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getPattern(): string
    {
        return (string) $this->evaluate($this->pattern);
    }

    public function mask(string|Closure $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask(): string
    {
        return (string) $this->evaluate($this->mask);
    }

    public function isNative(): bool
    {
        return false;
    }
}
