<?php

namespace App\Filament\Resources\PilotResource\Pages;

use App\Filament\Resources\PilotResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePilot extends CreateRecord
{
    protected static string $resource = PilotResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
