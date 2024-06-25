<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CouponResource;
use Illuminate\Contracts\Support\Htmlable;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

    /*
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    */

    public function getTitle(): string | Htmlable
    {
        return "Kupon adatok megadása";
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['custom_children_ids'] = $this->record->childrenCoupons()->where('source', '!=', 'Kiegészítő')->pluck('id')->toArray();
        return $data;
    }
}
