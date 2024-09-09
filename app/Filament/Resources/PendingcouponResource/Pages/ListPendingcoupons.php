<?php

namespace App\Filament\Resources\PendingcouponResource\Pages;

use App\Enums\CouponStatus;
use App\Filament\Resources\PendingcouponResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListPendingcoupons extends ListRecords
{
    protected static string $resource = PendingcouponResource::class;

    public function getTabs(): array
    {
        return [
            null => Tab::make('Mind'),
            CouponStatus::UnderProcess->getLabel() => Tab::make()->query(fn ($query) => $query->where('status', CouponStatus::UnderProcess))->icon(CouponStatus::UnderProcess->getIcon()),
            CouponStatus::CanBeUsed->getLabel() => Tab::make()->query(fn ($query) => $query->where('status', CouponStatus::CanBeUsed))->icon(CouponStatus::CanBeUsed->getIcon()),
            CouponStatus::Applicant->getLabel() => Tab::make()->query(fn ($query) => $query->where('status', CouponStatus::Applicant))->icon(CouponStatus::Applicant->getIcon()),
            CouponStatus::Used->getLabel() => Tab::make()->query(fn ($query) => $query->where('status', CouponStatus::Used))->icon(CouponStatus::Used->getIcon()),
            CouponStatus::Expired->getLabel() => Tab::make()->query(fn ($query) => $query->where('status', CouponStatus::Expired))->icon(CouponStatus::Expired->getIcon()),
        ];
    }
}
