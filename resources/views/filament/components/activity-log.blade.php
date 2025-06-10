<div class="flex flex-col overflow-auto font-medium text-sm" style="padding-right: 1rem; max-height: 250px;" wire:ignore>
    @php
        function getTickettype($id): ?string
        {
            return App\Models\Tickettype::find($id)?->fullname();
        }
        function getStatus($id): ?string
        {
            $status = collect(App\Enums\CouponStatus::cases())->where('value', $id)->first();
            return $status->getLabel();
        }
        function dateTime($string, $time = true): ?string
        {
            if ($time) {
                return Carbon\Carbon::parse($string)->format('Y.m.d. H:i');
            }
            return Carbon\Carbon::parse($string)->format('Y.m.d.');
        }
    @endphp

    @foreach ($activities as $activity)
        <div class="flex flex-col gap-1 w-full">
            <div class="flex items-center gap-1 w-full">
                <div class="flex justify-center items-center" style="padding-inline-start: 1px;">
                    <span class="ps-2 pe-1 text-primary-600 dark:text-primary-400">•</span>
                </div>
                <div class="flex gap-1 items-end w-full" style="margin-top: 1px;">
                    <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ $activity->event === 'created' ? __(ucfirst($activity->event)) : 'Módosítva' }}</span>
                    <span class="text-gray-400 dark:text-gray-500 text-xs w-full">{{ dateTime($activity->properties->first()['updated_at']) }}</span>
                    @if ($this instanceof App\Filament\Resources\PendingCouponResource\Pages\EditPendingCoupon)
                        <span class="text-gray-400 dark:text-gray-500 text-end text-xs w-full">{{ $activity->causer_type ? ($activity->causer_type::find($activity->causer_id)?->name ?? 'Rendszer') : 'Rendszer' }}</span>
                    @endif
                </div>
            </div>
            @if ($activity->event === 'updated')
                <div class="ps-2">
                    <div class="border-s border-gray-500 dark:border-white/10 ps-4 ms-1 pb-4 text-sm">
                        <table>
                            @foreach ($activity->properties['attributes'] as $attribute => $new)
                                <tr>
                                    @continue($attribute == 'updated_at')
                                    @php
                                        $old = $activity->properties['old'][$attribute];
                                        [$name, $old, $new] = match ($attribute) {
                                            'coupon_code' => ['Kupon azonosító 1', $old, $new],
                                            'auxiliary_coupon_code' => ['Kupon azonosító 2', $old, $new],
                                            'source' => ['Forrás', $old, $new],
                                            'children' => ['Gyermek', $old, $new],
                                            'adult' => ['Felnőtt', $old, $new],
                                            'tickettype_id' => ['Jegytípus', getTickettype($old), getTickettype($new)],
                                            'status' => ['Státusz', getStatus($old), getStatus($new)],
                                            'expiration_at' => ['Érvényesség', dateTime($old, false), dateTime($new, false)],
                                            'description' => ['Megjegyzés', $old, $new],
                                            default => [ucfirst($attribute), $old, $new],
                                        };
                                    @endphp
                                    <td class="w-max pe-2 align-top">{{ $name }}:</td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            @if (filled($old))
                                                <div class="text-red-600 dark:text-danger-400 align-top"
                                                    style="align-self: start;">{{ $old }}</div>
                                            @endif
                                            @if (filled($old) && filled($new))
                                                <div class="text-gray-400 dark:text-gray-500">-></div>
                                            @endif
                                            @if (filled($new))
                                                <div class="text-primary-600 dark:text-primary-400 align-top"
                                                    style="align-self: start;">{{ $new }}</div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
