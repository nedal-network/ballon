<x-filament-panels::page>
    <p class="text-gray-400">
        <b>Repülésre MINDEN utasnak szükséges egy kitöltött felelősség vállalási nyilatkozat</b>, aminek a linkjét 
        a jobb oldali zöld „Felelősségvállalási nyilatkozat” gombra kattintva éred el. Ezt a helyszínen gyűjtünk össze tőletek.
        <br>
        Ennek hiányában a repülést megtagadjuk és a jegyed elveszik, ha éppen nálunk sem lesz pót példány, amit ki tudnál tölteni.
    </p>
    <div class="flex gap-5 overflow-x-auto p-2">
        @forelse ($coupons as $coupon)
            <div class="clickable card @if ($coupon->id === $coupon_id) selected @endif grid min-w-max justify-between" wire:click="$set('coupon_id', {{ $coupon->id }})">
                <div class="font-semibold">{{ $coupon->coupon_code }}</div>
                <div class="grid">
                    <div>{{ $coupon->source }} kupon</div>
                    <div class="relative grid grid-cols-2">
                        @php
                            $extra_adult = 0;
                            $extra_children = 0;
                            if ($coupon->childrenCoupons) {
                                $extra_adult += $coupon->childrenCoupons->map(fn($coupon) => $coupon->adult)->sum();
                                $extra_children += $coupon->childrenCoupons->map(fn($coupon) => $coupon->children)->sum();
                            }
                        @endphp
                        <div class="flex flex-col justify-self-start">
                            @if ($coupon->adult || $extra_adult)
                                <div class="flex">
                                    <span class="quantity">{{ $coupon->adult . ($extra_adult ? '+' . $extra_adult : '') }} </span>
                                    <span class="quantity-description">felnőtt</span>
                                </div>
                            @endif
                            @if ($coupon->children || $extra_children)
                                <div class="flex">
                                    <span class="quantity">{{ $coupon->children . ($extra_children ? '+' . $extra_children : '') }} </span>
                                    <span class="quantity-description">gyerek</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card w-full">
                <x-filament-tables::empty-state :actions="[]" :description="null" :heading="__('filament-tables::table.empty.heading')" :icon="'heroicon-o-x-mark'" />
            </div>
        @endforelse
    </div>

    @if ($this->events !== false)
        <div class="flex" style="padding-bottom:10px; border-bottom:1px solid rgba(128,128,128,0.2);">
            <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ $this->coupon->coupon_code }}</h1>
            <span class="pl-2 pt-1.5 sm:pt-2.5">{{ $this->coupon->source }} kupon</span>
        </div>
        @if ($this->events->count() && $this->regions->count())
            <x-filament::tabs label="Content tabs">
                <x-filament::tabs.item :active="$activeTab === 'all'" wire:click="$set('activeTab', 'all')">
                    Mind
                </x-filament::tabs.item>
                @foreach ($this->regions as $id => $region)
                    <x-filament::tabs.item :active="$activeTab === $id" wire:click="$set('activeTab', {{ $id }})">
                        {{ $region }}
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>
        @endif
        <div class="flex w-full flex-wrap gap-x-5 gap-y-8 overflow-x-auto p-2">
            @forelse ($this->events->groupBy('date') as $date => $events)
                <div class="grid grid-flow-col gap-2">
                    <div>
                        <div class="card max-h-min !py-2">
                            <div class="pb-2">{{ Carbon\Carbon::parse($date)->translatedFormat('Y.m.d.') }}</div>
                            @foreach ($events as $event)
                                @php
                                    $signed = $event->isChecked($this->coupon->id);
                                    $finalized = $event->status == App\Enums\AircraftLocationPilotStatus::Finalized;
                                    $selected = $event->coupons->firstWhere('id', $this->coupon->id)?->pivot->status == 1;
                                @endphp
                                <div class="card @if (($signed && !$finalized && !$this->coupon->is_used) || ($finalized && $signed && $selected)) border-green-500/80 @else dark:border-white/20 @endif @if ($signed && $finalized && $selected) bg-green-600/10 dark:bg-[#4ade80]/10 @elseif($finalized) bg-zinc-200/20 text-zinc-400 @endif mb-4 grid min-w-[220px] gap-2 border-2" wire:key="event-{{ $event->id }}">
                                    <div class="flex justify-between items-center h-full">
                                        <div>{{ Carbon\Carbon::parse($event->time)->format('H:i') }}</div>
                                        <div class="flex justify-between items-center gap-2">
                                            @if ($finalized)
                                                <div class="@if ($signed && $finalized && $selected) text-green-600 @elseif($finalized) text-zinc-400 @endif">@svg('tabler-flag-check')</div>
                                            @endif
                
                                            @if ($signed && $finalized && $selected)
                                                <div class="font-semibold text-green-600 dark:text-green-400/80">Résztveszek</div>
                                            @elseif($finalized)
                                                <div class="font-semibold text-zinc-400">Lezárva</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex justify-between" style="text-alaign: left;font-size:9pt;">
                                        @if (filled($event->public_description))
                                            <b>Info: </b>{{ $event->public_description }}
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="@if (($signed && !$finalized) || ($finalized && $signed && $selected) || (!$signed && !$finalized)) text-red-500 @else text-red-500/50 @endif">
                                            <x-heroicon-c-map-pin class="w-6" />
                                        </div>
                                        <span>{{ $event->region->name }}</span>
                                    </div>
                
                                    <div class="flex justify-between gap-2">
                                        <div class="flex items-center justify-self-center text-zinc-400">
                                            <x-iconoir-user class="w-5" />
                                            <span class="py-2 ps-1 text-sm font-semibold">{{ $event->coupons->sum('members_count') }}</span>
                                        </div>
                                        <div>
                                            @if (!$signed && !$this->coupon->isExpired())
                                                <x-filament::button class="!bg-blue-600 hover:!bg-blue-700" wire:click="checkIn({{ $event->id }})" wire:key="check-in-{{ $event->id }}">Jelölöm</x-filament::button>
                                            @elseif(!$signed && $this->coupon->isExpired())
                                                <x-filament::button class="!bg-gray-600/50 hover:!bg-gray-700/50" wire:click="checkIn({{ $event->id }})" wire:key="check-in-{{ $event->id }}" disabled>Jelölöm</x-filament::button>
                                            @elseif(now() < Carbon\Carbon::parse($event->date)->subWeek() && $selected && $finalized)
                                                <x-filament::button class="!bg-red-600 hover:!bg-red-700" wire:click="checkOut({{ $event->id }})" wire:key="check-out-{{ $event->id }}">Kiszállok</x-filament::button>
                                            @elseif(now() < Carbon\Carbon::parse($event->date)->subWeek() && $selected)
                                                <x-filament::button class="!bg-red-600 hover:!bg-red-700" wire:click="checkOut({{ $event->id }})" wire:key="check-out-{{ $event->id }}">Törlés</x-filament::button>
                                            @elseif($selected)
                                                <x-filament::button class="!bg-gray-600/50 hover:!bg-gray-700/50" wire:click="checkOut({{ $event->id }})" wire:key="check-out-{{ $event->id }}" disabled>Törlés</x-filament::button>
                                            @else
                                                <x-filament::button class="!bg-red-600 hover:!bg-red-700" wire:click="checkOut({{ $event->id }})" wire:key="check-out-{{ $event->id }}">Törlés</x-filament::button>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <x-filament::badge :color="$event->status->getColor()" :icon="$event->status->getIcon()">
                                            {{ $event->status->getLabel() }}
                                        </x-filament::badge>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="card w-full">
                    <x-filament-tables::empty-state :actions="[]" :description="null" :heading="__('filament-tables::table.empty.heading')" :icon="'heroicon-o-x-mark'" />
                </div>
            @endforelse
        </div>
    @endif
</x-filament-panels::page>
