<?php

namespace App\Filament\Pages;

use App\Enums\AircraftLocationPilotStatus;
use App\Enums\CouponStatus;
use App\Filament\Resources\CouponResource;
use App\Mail\JoinToEvent;
use App\Mail\LeaveFromEvent;
use App\Models\AircraftLocationPilot;
use App\Models\Checkin as CheckinModel;
use App\Models\Coupon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;

class Checkin extends Page
{
    use HasPageShield;

    public $activeTab = 'all';

    public $coupons;

    public $coupon_id;

    public $regions;

    protected static ?string $title = 'Repülési időpontok';

    protected ?string $heading = 'Repülési időpontok'; // Repülési időpontjaid

    protected static ?string $navigationLabel = 'Repülési időpontok';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.checkin';

    protected static ?int $navigationSort = 2;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('redirect-to-responsibility-statement')->label('Felelősségvállalási nyilatkozat')
                ->color('success')
                ->url('https://ballonozz.hu/felelossegvallalasi-nyilatkozat/', true),
            Action::make('redirect-to-coupon')->label('Kuponjaim')
                ->color('info')
                ->url(CouponResource::getUrl()),
        ];
    }

    public function mount()
    {
        $this->coupons = Coupon::query()
            ->with('aircraftLocationPilots')
            ->orderBy('source')
            ->orderBy('coupon_code')
            ->get()
            ->filter(fn ($coupon) => $coupon->isActive || $coupon->aircraftLocationPilots->whereNotIn('status', [AircraftLocationPilotStatus::Deleted])->count());

        if ($this->coupons->count()) {
            $this->coupon_id = $this->coupons->first()->id;
        }
    }

    #[Computed]
    public function coupon()
    {
        if ($this->coupon_id === null) {
            return null;
        }

        return $this->coupons->where('id', $this->coupon_id)->first();
    }

    #[Computed]
    public function events()
    {
        if ($this->coupon === null) {
            return false;
        }

        $couponEvents = $this->coupon->aircraftLocationPilots->whereNotIn('status', [AircraftLocationPilotStatus::Deleted]);

        if ($this->coupon->isExpired() && ! $couponEvents->count()) {
            return new EloquentCollection();
        } elseif ($this->coupon->isExpired()) {
            $query = (new EloquentCollection($couponEvents))->toQuery();
        } else {
            $query = AircraftLocationPilot::query();
        }

        $events = $query
            ->where('date', '>=', now()->format('Y-m-d'))
            ->whereIn('status', [AircraftLocationPilotStatus::Published, AircraftLocationPilotStatus::Finalized])
            ->withWhereHas('aircraft', function ($query) {
                $query->where('number_of_person', '>=', $this->coupon->membersCount);
            })
            ->withWhereHas('aircraft.tickettypes', function ($query) {
                $query->where('id', $this->coupon->tickettype->id);
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $this->regions = $events->pluck('region.name', 'region.id')->unique();

        if ($this->activeTab === 'all') {
            return $events;
        }

        return $events->where('region_id', $this->activeTab);
    }

    public function checkIn($aircraftLocationPilotId)
    {
        $event = AircraftLocationPilot::find($aircraftLocationPilotId);
        CheckinModel::create([
            'aircraft_location_pilot_id' => $event->id,
            'coupon_id' => $this->coupon->id,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);

        Mail::to(Auth::user())->queue(new JoinToEvent(
            user: Auth::user(),
            coupon: $this->coupon,
            event: $event
        ));
    }

    public function checkOut($aircraftLocationPilotId)
    {
        $event = AircraftLocationPilot::find($aircraftLocationPilotId);
        CheckinModel::where('aircraft_location_pilot_id', $event->id)
            ->where('coupon_id', $this->coupon->id)
            ->delete();

        if ($this->coupon->status == CouponStatus::Applicant && !$this->coupon->isExpired()) {
            $this->coupon->update(['status' => CouponStatus::CanBeUsed]);
        }

        if ($this->coupon->isExpired()) {
            $this->coupon->update(['status' => CouponStatus::Expired]);
        }

        Mail::to(Auth::user())->queue(new LeaveFromEvent(
            user: Auth::user(),
            coupon: $this->coupon,
            event: $event
        ));
    }
}
