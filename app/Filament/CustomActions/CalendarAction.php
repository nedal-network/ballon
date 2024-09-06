<?php

namespace App\Filament\CustomActions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;

class CalendarAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'calendar';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Naptár nézet');
        $this->color('info');
        $this->url(route('filament.admin.resources.aircraft-location-pilots.calendar').'?view='.env('DEFAULT_CALENDAR_VIEW', 'havi'));
    }
}
