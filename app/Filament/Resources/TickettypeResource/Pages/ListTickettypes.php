<?php

namespace App\Filament\Resources\TickettypeResource\Pages;

use App\Filament\Resources\TickettypeResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTickettypes extends ListRecords
{
    protected static string $resource = TickettypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('Mind'),
            'Hőlégballonos jegytípusok' => Tab::make()->query(fn ($query) => $query->where('aircrafttype', '0'))->icon('iconoir-hot-air-balloon'),
            'Kisrepülős jegytípusok' => Tab::make()->query(fn ($query) => $query->where('aircrafttype', '1'))->icon('iconoir-airplane'),
        ];
    }
}
