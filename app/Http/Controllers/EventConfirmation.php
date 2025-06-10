<?php

namespace App\Http\Controllers;

use App\Models\AircraftLocationPilot;
use Illuminate\Http\Request;

class EventConfirmation extends Controller
{
    public function index(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $event = AircraftLocationPilot::findOrFail($request->event_id);
        $coupon = $event->coupons()->findOrFail($request->coupon_id);

        if (! $coupon->pivot->confirmed_at && $coupon->pivot->status === 1) {
            $event->coupons()->updateExistingPivot($coupon->id, ['confirmed_at' => now()]);
        }

        $message = match (true) {
            $coupon->pivot->confirmed_at !== null => 'Visszaigazolva!',
            $coupon->pivot->status === 1 => 'Sikeres visszaigazolás!',
            default => 'Nem lehet visszaigazolni!',
        };

        return view('event-confirmation', compact('message'));
    }
}
