<?php

namespace App\Filament\Resources\AircraftLocationPilotResource\Pages;

use App\Models\Coupon;
use App\Models\Checkin;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Enums\AircraftLocationPilotStatus;
use App\Enums\CouponStatus;
use App\Filament\Resources\AircraftLocationPilotResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ListCheckins extends Page
{
    use InteractsWithRecord;
    protected static string $resource = AircraftLocationPilotResource::class;
    protected static string $view = 'filament.resources.aircraft-location-pilot-resource.pages.list-checkins';
    protected static ?string $title = 'Jelentkezők';
    public $selectedCoupons = [];
    public $alreadyCheckedCoupons;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('finalize')->label('Véglegesít')
                ->fillForm(['location_id' => $this->record->location_id])
                ->form([
                    Select::make('location_id')
                        ->label('Helyszín')
                        ->options($this->record->region->locations->pluck('name', 'id'))
                        ->required(),       
                ])
                ->action(function (array $data): void {

                    $deselectedCoupons = $this->record->coupons()->wherePivotNotIn('coupon_id', $this->selectedCoupons)->pluck('coupon_id')->toArray();

                    Coupon::whereIn('id', $this->selectedCoupons)->update(['status' => CouponStatus::Applicant]);
                    Coupon::whereIn('id', $deselectedCoupons)->update(['status' => CouponStatus::CanBeUsed]);
                    
                    $data['status'] = AircraftLocationPilotStatus::Finalized;

                    $this->record->update($data);

                    Notification::make()
                        ->success()
                        ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                        ->send();
                })
        ];
    }
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->selectedCoupons = $this->record->coupons
            ->where('status', CouponStatus::Applicant)
            ->map(fn ($coupon) => $coupon->id)
            ->toArray();

        $this->alreadyCheckedCoupons = array_filter(Coupon::with('aircraftLocationPilots')->where('status', CouponStatus::Applicant)->get()->map(function ($coupon) {
            if (count($coupon->aircraftLocationPilots->where('pivot.aircraft_location_pilot_id', '!=', $this->record->id))) {
                return $coupon->id;
            }
        })->toArray());
    }
}
