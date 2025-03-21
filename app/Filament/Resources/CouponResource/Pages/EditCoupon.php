<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class EditCoupon extends EditRecord
{
    protected static string $resource = CouponResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Kupon adatok megadása';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['custom_children_ids'] = $this->record->childrenCoupons()->where('source', '!=', 'Kiegészítő')->pluck('id')->toArray();
        $data['liked_regions'] = $this->record->likedRegions->pluck('id')->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->likedRegions()->sync($data['liked_regions']);
        unset($data['liked_regions']);
        $record->update($data);

        return $record;
    }
}
