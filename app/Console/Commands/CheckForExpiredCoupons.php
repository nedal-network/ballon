<?php

namespace App\Console\Commands;

use App\Enums\CouponStatus;
use App\Models\Coupon;
use Illuminate\Console\Command;

class CheckForExpiredCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon:check-for-expired-coupons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A lejárt kuponok státuszát billentsük át lejártra';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Coupon::withoutGlobalScopes()
            ->where('expiration_at', '<', today())
            ->whereIn('status', [CouponStatus::CanBeUsed, CouponStatus::UnderProcess])
            ->update(['status' => CouponStatus::Expired]);
    }
}
