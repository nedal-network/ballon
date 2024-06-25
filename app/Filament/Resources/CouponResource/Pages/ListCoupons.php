<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Actions;
use App\Enums\CouponStatus;
use App\Filament\Pages\Checkin;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CouponResource;

class ListCoupons extends ListRecords
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('redirect-to-chekin')->label('Repülés időpontok')
                ->color('info')
                ->url(Checkin::getUrl()),
            Actions\CreateAction::make()->label('Új kupon megadása'),
            /*Actions\Action::make('edit')->url(route('posts.edit', ['post' => $this->post])),*/
        ];
    }

    public function getTabs(): array
    {
        return [
            /*
            null => Tab::make('Mind'),
            'Figyelmeztetések' => Tab::make()->query(fn ($query) => $query->MissingData())
                ->icon('tabler-alert-triangle')
                ->badgeColor('danger'),
            'Feldolgozás alatt' => Tab::make()->query(fn ($query) => $query->where('status', '0'))->icon('tabler-progress-check')->badgeColor('warning'),
            'Felhasználható' => Tab::make()->query(fn ($query) => $query->where('status', '1')->orwhere('status', '2'))->icon('tabler-discount-check')->badgeColor('success'),
            'Felhasznált' => Tab::make()->query(fn ($query) => $query->where('status', '3'))->icon('tabler-circle-x')->badgeColor('danger'),
            */
        ];
    }

}