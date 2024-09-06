<?php

namespace App\Filament\Resources\TickettypeResource\Pages;

use App\Filament\Resources\TickettypeResource;
use App\Models\Tickettype;
use Filament\Resources\Pages\CreateRecord;

class CreateTickettype extends CreateRecord
{
    protected static string $resource = TickettypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $aircrafttype = $data['aircrafttype'];
        $first_tickettype_check = Tickettype::where('aircrafttype', $aircrafttype)->get()->count();
        if ($data['default'] == 0) {
            $data['default'] = 1;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
