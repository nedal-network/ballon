<x-filament-panels::page>
    <div class="flex gap-5 p-2 overflow-x-auto">
        @forelse ($coupons as $coupon)
            <div class="clickable card grid justify-between min-w-max @if ($coupon->id === $coupon_id) selected @endif"
                wire:click="$set('coupon_id', {{ $coupon->id }})">
                <div class="font-semibold">{{ $coupon->coupon_code }}</div>
                <div class="grid">
                    <div>{{ $coupon->source }} kupon</div>
                    <div class="grid grid-cols-2 relative">
                        
                        <div class="flex flex-col justify-self-start">
                            @if ($coupon->adult)  
                                <div class="flex">
                                    <span class="quantity">{{ $coupon->adult }} </span>
                                    <span class="quantity-description">felnőtt</span>
                                </div>
                            @endif
                            @if ($coupon->children)    
                                <div class="flex">
                                    <span class="quantity">{{ $coupon->children }} </span>
                                    <span class="quantity-description">gyerek</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card w-full">
                <x-filament-tables::empty-state
                :actions="[]"
                :description="null"
                :heading="__('filament-tables::table.empty.heading')"
                :icon="'heroicon-o-x-mark'"
                />
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
                <x-filament::tabs.item 
                    :active="$activeTab === 'all'"
                    wire:click="$set('activeTab', 'all')">
                    Mind
                </x-filament::tabs.item>
                @foreach ($this->regions as $id => $region)
                    <x-filament::tabs.item 
                        :active="$activeTab === $id"
                        wire:click="$set('activeTab', {{ $id }})">
                        {{ $region }}
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>
        @endif
        <div class="flex flex-wrap gap-x-5 gap-y-8 w-full p-2 overflow-x-auto">
            @foreach ($this->events as $event)
                @php
                    $selected = $event->isChecked($this->coupon->id);
                    $finalized = $event->status == App\Enums\AircraftLocationPilotStatus::Finalized;
                    $checked = $event->coupons()->find($this->coupon)?->pivot->status == 1;
                @endphp
                @if ($loop->first)
                    @php
                        $fly_at = $event->date;
                    @endphp
                    <div class="grid grid-flow-col gap-2">
                        <div><div class="card max-h-min !py-2">
                        
                    <div class="pb-2">{{ Carbon\Carbon::parse($fly_at)->translatedFormat('Y.m.d.') }}</div>
                @elseif($fly_at != $event->date && $fly_at != null)
                    @php
                        $fly_at = $event->date;
                    @endphp
                    </div></div></div><div class="grid grid-flow-col gap-2">
                        <div>
                        <div class="card max-h-min !py-2">
                    <div class="pb-2">{{ Carbon\Carbon::parse($fly_at)->translatedFormat('Y.m.d.') }}</div>
                @endif

                <div class="card mb-4 grid gap-2 min-w-[220px] border-2 @if($selected && !$finalized && !$this->coupon->is_used || $finalized && $selected && $checked) border-green-500/80 @else dark:border-white/20 @endif @if($selected && $finalized && $checked) bg-green-600/10 dark:bg-[#4ade80]/10 @elseif($finalized) bg-zinc-200/20 text-zinc-400 @endif">
                    <div class="flex justify-between">
                        <div>{{ Carbon\Carbon::parse($event->time)->format('H:i') }}</div>
                        @if ($finalized)
                            <div class="@if($selected && $finalized && $checked) text-green-600 @elseif($finalized) text-zinc-400 @endif">@svg('tabler-flag-check')</div>
                        @endif

                        @if($selected && $finalized && $checked) 
                                <div class="text-green-600 dark:text-green-400/80 font-semibold p-1.5">Résztveszek</div>
                        @elseif($finalized)
                                <div class="text-zinc-400 font-semibold p-1.5">Lezárva</div>
                        @endif
                    </div>

                    <div class="flex justify-between" style="text-alaign: left;font-size:9pt;">
                        <b>Info: </b>{{ $event->public_description }}
                    </div>
                    
                    <div class="flex gap-2">
                        <div class="@if($selected && !$finalized || $finalized && $selected && $checked || !$selected && !$finalized) text-red-500 @else text-red-500/50 @endif"">
                            <x-heroicon-c-map-pin class="w-6"/>
                        </div>
                        <span>{{ $event->region->name }}</span>
                    </div>

                    <div class="flex justify-between gap-2">
                        <div class="flex text-zinc-400 justify-self-center items-center">
                            <x-iconoir-user class="w-5"/>
                            <span class="ps-1 py-2 text-sm font-semibold">{{ $event->coupons->map(fn ($coupon) => $coupon->membersCount)->sum() }}</span>
                        </div>
                        <br>
                        <div class="flex text-zinc-400 justify-self-center items-center">
                            <x-iconoir-weight-alt class="w-5"/>
                            <span class="ps-1 py-2 text-sm font-semibold">{{ $event->coupons->map(fn ($coupon) => $coupon->membersBodyWeight)->sum() }}kg</span>
                        </div>
                        <div>
                        
                            @if(!$selected && !$this->coupon->is_used)
                                <x-filament::button wire:click="checkIn({{ $event->id }})" class="!bg-blue-600 hover:!bg-blue-700" >Beszállok</x-filament::button>
                            @elseif(now() < Carbon\Carbon::parse($event->date)->subWeek() && $checked)
                                <x-filament::button class="!bg-red-600 hover:!bg-red-700" wire:click="checkOut({{ $event->id }})">Kiszállok</x-filament::button>
                            @elseif($checked)
                                <x-filament::button class="!bg-gray-600/50 hover:!bg-gray-700/50" wire:click="checkOut({{ $event->id }})" disabled>Kiszállok</x-filament::button>
                            @else
                                <x-filament::button class="!bg-red-600 hover:!bg-red-700" wire:click="checkOut({{ $event->id }})">Kiszállok</x-filament::button>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        
            @if (!$this->events->count())
                <div class="card w-full">
                    <x-filament-tables::empty-state
                    :actions="[]"
                    :description="null"
                    :heading="__('filament-tables::table.empty.heading')"
                    :icon="'heroicon-o-x-mark'"
                    />
                </div>    
            @else
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
