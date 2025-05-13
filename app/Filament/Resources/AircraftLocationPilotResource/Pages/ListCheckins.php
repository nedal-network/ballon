<?php

namespace App\Filament\Resources\AircraftLocationPilotResource\Pages;

use App\Enums\AircraftLocationPilotStatus;
use App\Enums\CouponStatus;
use App\Filament\Resources\AircraftLocationPilotResource;
use App\Mail\EventFinalized;
use App\Mail\KickedFromEvent;
use App\Models\Coupon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Mail;

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
                ->disabled($this->record->status === AircraftLocationPilotStatus::Deleted)
                ->action(function (array $data): void {

                    $unselectedCoupons = $this->record->coupons()->wherePivotNotIn('coupon_id', $this->selectedCoupons)->pluck('coupon_id')->toArray();

                    $informations = $this->record->coupons->map(function ($coupon) {
                        if (in_array($coupon->id, $this->selectedCoupons) && $coupon->status != CouponStatus::Applicant) {
                            return ['user' => $coupon->user, 'coupon' => $coupon];
                        }
                    })->filter();

                    $kickedInformations = $this->record->coupons->where('pivot.status', 1)->map(function ($coupon) use ($unselectedCoupons) {
                        if (in_array($coupon->id, $unselectedCoupons) && $coupon->status === CouponStatus::Applicant) {
                            return ['user' => $coupon->user, 'coupon' => $coupon];
                        }
                    })->filter();

                    $this->record->coupons()->updateExistingPivot($this->selectedCoupons, ['status' => 1]);
                    $this->record->coupons()->updateExistingPivot($unselectedCoupons, ['status' => 0]);

                    Coupon::whereIn('id', $this->selectedCoupons)->update(['status' => CouponStatus::Applicant]);
                    Coupon::whereIn('id', $unselectedCoupons)->update(['status' => CouponStatus::CanBeUsed]);

                    foreach ($informations as $info) {
                        Mail::to($info['user'])->queue(new EventFinalized(
                            user: $info['user'],
                            coupon: $info['coupon'],
                            event: $this->record
                        ));
                    }

                    foreach ($kickedInformations as $info) {
                        Mail::to($info['user'])->queue(new KickedFromEvent(
                            user: $info['user'],
                            coupon: $info['coupon'],
                            event: $this->record
                        ));
                    }

                    $data['status'] = AircraftLocationPilotStatus::Finalized;

                    $this->record->update($data);

                    Notification::make()
                        ->success()
                        ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
                        ->send();
                }),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->record->coupons->where('pivot.status', 1)
            ->map(fn ($coupon) => $this->selectedCoupons[] = $coupon->id);

        $coupons = Coupon::with('aircraftLocationPilots')->where('status', CouponStatus::Applicant)->get();

        $this->alreadyCheckedCoupons = $coupons
            ->filter(function ($coupon) {
                return $coupon->aircraftLocationPilots
                    ->where('status', '!=', AircraftLocationPilotStatus::Deleted)
                    ->where('pivot.aircraft_location_pilot_id', '!=', $this->record->id)
                    ->where('pivot.status', 1)->count();
            })
            ->pluck('id')
            ->toArray();
    }
}
