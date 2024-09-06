<?php

namespace App\Filament\Resources\AircraftLocationPilotResource\Pages;

use App\Filament\Resources\AircraftLocationPilotResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAircraftLocationPilot extends CreateRecord
{
    protected static string $resource = AircraftLocationPilotResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
