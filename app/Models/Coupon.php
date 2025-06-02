<?php

namespace App\Models;

use App\Enums\AircraftLocationPilotStatus;
use App\Enums\AircraftType;
use App\Enums\CouponStatus;
use App\Mail\CouponApproved;
use App\Mail\CouponUnderProcess;
use App\Mail\CouponExpired;
use App\Models\Scopes\ClientScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ScopedBy([ClientScope::class])]
class Coupon extends Model
{
    use LogsActivity;

    protected $table = 'coupons';

    protected $guarded = ['custom_children_ids'];

    private $checkList = [];

    protected $casts = [
        'status' => CouponStatus::class,
        'aircraft_type' => AircraftType::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted(): void
    {
        static::created(function (self $coupon) {
            if ($coupon->status === CouponStatus::UnderProcess) {
                Mail::to(env('INFO_EMAIL', 'info@utasfoglalo.hu'))->queue(new CouponUnderProcess($coupon));
            }
        });
        static::deleting(function (self $coupon) {
            $coupon->activities()->delete();
            $coupon->aircraftLocationPilots()->sync([]);
        });
        static::updating(function (self $coupon) {
            if ($coupon->isExpired() && $coupon->status !== CouponStatus::Expired) {
                $coupon->status = CouponStatus::Expired;
            }
        });
        static::updated(function (self $coupon) {

            switch ($coupon->status) {
                case CouponStatus::CanBeUsed:
                    switch ($coupon->getOriginal('status')) {
                        case CouponStatus::UnderProcess:
                            Mail::to($coupon->user)->queue(new CouponApproved(
                                user: $coupon->user,
                                coupon: $coupon
                            ));
                            break;
                    }
                    break;

                    // case CouponStatus::Applicant: --> mail: App\Filament\Resources\AircraftLocationPilotResource\Pages\ListCheckins.php

                    // case CouponStatus::Used: --> mail: App\Models\AircraftLocationPilot.php

                case CouponStatus::Expired:
                    Mail::to($coupon->user)->queue(new CouponExpired(
                        user: $coupon->user,
                        coupon: $coupon
                    ));
                    break;
            }
        });
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aircraftLocationPilots()
    {
        return $this->belongsToMany(AircraftLocationPilot::class, 'checkins', 'coupon_id', 'aircraft_location_pilot_id')->withPivot('status');
    }

    public function tickettype()
    {
        return $this->hasOne(Tickettype::class, 'id', 'tickettype_id');
    }

    public function likedRegions()
    {
        return $this->belongsToMany(Region::class, 'liked_regions', 'coupon_id', 'region_id', 'id', 'id');
    }

    private function validatePassengersData(self $coupon): void
    {
        foreach ($coupon->passengers as $p) {
            if ($p->firstname && $p->lastname && $p->date_of_birth && $p->id_card_number && $p->body_weight) {
                $this->checkList[] = true;
            } else {
                $this->checkList[] = false;
            }
        }
    }

    protected function isCheckedToAnEvent(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                return $this->aircraftLocationPilots
                    ->whereIn('status', [AircraftLocationPilotStatus::Published, AircraftLocationPilotStatus::Finalized])
                    ->count();
            }
        );
    }

    public function isExpired(): bool
    {
        return Carbon::parse($this->expiration_at) < today() || $this->status == CouponStatus::Expired;
    }

    public function isVirtual(): bool
    {
        return $this->source === 'Kiegészítő';
    }

    protected function isValid(): Attribute
    {
        return Attribute::make(
            get: function () {
                $noOneMissing = $this->membersCount == ($this->passengers->count() + $this->childrenCoupons?->map(fn ($coupon) => $coupon->passengers->count())->sum() ?? 0);

                $this->validatePassengersData($this);
                if ($this->childrenCoupons) {
                    $this->childrenCoupons->map(fn ($coupon) => $this->validatePassengersData($coupon));
                }

                return count(array_unique($this->checkList)) === 1 && array_unique($this->checkList)[0] === true && $noOneMissing;
            },
        );
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: function () {

                $isParent = $this->parent_id === null;

                if ($this->status == CouponStatus::Applicant) { // Ha már beválogatták láthassa a kuponját
                    return true;
                }

                if (! $this->isExpired() && in_array($this->status, [CouponStatus::CanBeUsed, CouponStatus::Applicant]) && $isParent && $this->isValid) {
                    return true;
                }

                return false;
            },
        );
    }

    protected function missingData(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, [CouponStatus::CanBeUsed]) && ! $this->isValid,
        );
    }

    protected function membersCount(): Attribute
    {
        return Attribute::make(
            get: function () {

                $membersCount = $this->adult + $this->children;

                if ($this->childrenCoupons) {
                    return $membersCount + $this->childrenCoupons->map(fn ($coupon) => $coupon->adult + $coupon->children)->sum();
                }

                return $membersCount;
            },
        );
    }

    protected function membersBodyWeight(): Attribute
    {
        return Attribute::make(
            get: function () {

                $bodyWeight = $this->passengers->sum('body_weight');

                if ($this->childrenCoupons) {
                    return $bodyWeight + $this->childrenCoupons->map(fn ($coupon) => $coupon->passengers->sum('body_weight'))->sum();
                }

                return $bodyWeight;
            },
        );
    }

    public function parentCoupon()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function childrenCoupons()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function updateAsSystem(array $values): bool
    {
        $user = auth()->user();

        if ($user === null) {
            return $this->update($values);
        }

        auth()->logout();
        $isUpdated = $this->update($values);
        auth()->login($user, ! empty($user->getRememberToken()));

        return $isUpdated;
    }
}
