<?php

namespace App\Filament\Resources\AircraftLocationPilotResource\Pages;

use App\Enums\AircraftLocationPilotStatus;
use App\Filament\CustomActions;
use App\Filament\Resources\AircraftLocationPilotResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListAircraftLocationPilots extends ListRecords
{
    protected static string $resource = AircraftLocationPilotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CustomActions\CalendarAction::make(),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Mind'),
            'Tervezett' => Tab::make()->query(fn ($query) => $query->where('status', AircraftLocationPilotStatus::Draft))->icon('tabler-player-pause'),
            'Publikált' => Tab::make()->query(fn ($query) => $query->where('status', AircraftLocationPilotStatus::Published))->icon('tabler-player-play'),
            'Véglegesített' => Tab::make()->query(fn ($query) => $query->where('status', AircraftLocationPilotStatus::Finalized))->icon('tabler-flag-check'),
            'Végrehajtott' => Tab::make()->query(fn ($query) => $query->where('status', AircraftLocationPilotStatus::Executed))->icon('tabler-player-stop'),
            'Törölt' => Tab::make()->query(fn ($query) => $query->where('status', AircraftLocationPilotStatus::Deleted))->icon('tabler-playstation-x'),
        ];
    }
}
