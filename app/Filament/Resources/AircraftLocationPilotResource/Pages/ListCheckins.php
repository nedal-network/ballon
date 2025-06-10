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

    public $confirmedCoupons = [];

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

                    $informations = $this->record->coupons
                        ->filter(function (Coupon $coupon) {
                            if ($coupon->user->deleted_at) {
                                return false;
                            }

                            return $coupon->status !== CouponStatus::Applicant && in_array($coupon->id, $this->selectedCoupons);
                        });

                    $kickedInformations = $this->record->coupons->where('pivot.status', 1)
                        ->filter(function (Coupon $coupon) use ($unselectedCoupons) {
                            if ($coupon->user->deleted_at) {
                                return false;
                            }

                            return $coupon->status === CouponStatus::Applicant && in_array($coupon->id, $unselectedCoupons);
                        });

                    $this->record->coupons()->updateExistingPivot($this->selectedCoupons, ['status' => 1]);
                    $this->record->coupons()->updateExistingPivot($unselectedCoupons, ['status' => 0, 'confirmed_at' => null]);

                    Coupon::withoutGlobalScopes()->whereIn('id', $this->selectedCoupons)->get()
                        ->each(fn (Coupon $coupon) => $coupon->updateAsSystem(['status' => CouponStatus::Applicant]));

                    Coupon::withoutGlobalScopes()->whereIn('id', $unselectedCoupons)->get()
                        ->each(fn (Coupon $coupon) => $coupon->updateAsSystem(['status' => CouponStatus::CanBeUsed]));

                    foreach ($informations as $coupon) {
                        Mail::to($coupon->user)->queue(new EventFinalized(
                            user: $coupon->user,
                            coupon: $coupon,
                            event: $this->record
                        ));
                    }

                    foreach ($kickedInformations as $coupon) {
                        Mail::to($coupon->user)->queue(new KickedFromEvent(
                            user: $coupon->user,
                            coupon: $coupon,
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

    public function toggleConfirmation(int $coupon_id)
    {
        if (in_array($coupon_id, $this->confirmedCoupons)) {
            $this->confirmedCoupons = array_diff($this->confirmedCoupons, [$coupon_id]);
            $this->record->coupons()->updateExistingPivot($coupon_id, ['confirmed_at' => null]);
        } else {
            $this->record->coupons()->updateExistingPivot($coupon_id, ['confirmed_at' => now()]);
            $this->confirmedCoupons[] = $coupon_id;
        }
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        foreach ($this->record->coupons->where('pivot.status', 1) as $coupon) {
            $this->selectedCoupons[] = $coupon->id;
        }

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

        $this->confirmedCoupons = $this->record->coupons
            ->where('pivot.confirmed_at', '!=', null)
            ->pluck('id')
            ->unique()
            ->toArray();
    }
}
