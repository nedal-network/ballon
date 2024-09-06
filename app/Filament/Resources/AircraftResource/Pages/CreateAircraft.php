<?php

namespace App\Filament\Resources\AircraftResource\Pages;

use App\Filament\Resources\AircraftResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAircraft extends CreateRecord
{
    protected static string $resource = AircraftResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
