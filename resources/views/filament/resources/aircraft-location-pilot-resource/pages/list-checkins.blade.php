<x-filament-panels::page>
<div class="flex flex-col h-[calc(100vh-64px)] gap-y-8 py-8">
        <header>
            <div class="grid grid-cols-2">
                <div>
                    @php
                        $subHeading = [];
                        $record->aircraft?->name && $subHeading[] = $record->aircraft->name;
                        $record->region?->name   && $subHeading[] = $record->region->name;
                        $record->location?->name && $subHeading[] = $record->location->name;
                    @endphp
                    <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ Carbon\Carbon::parse($record->date . ' ' . $record->time)->translatedFormat('Y.m.d., H:i') }}</h1>
                    <h2 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">{{ implode(', ', $subHeading) }}</h2>
                    
                    
                    
                </div>
                @php
                    $bodiesWeight = 0;
                    $membersCount = 0;
                    foreach ($record->coupons->whereIn('id', $selectedCoupons) as $coupon) {
        
                        $bodiesWeight += $coupon->membersBodyWeight;
                        $membersCount += $coupon->membersCount;
                    }
                @endphp
                <div class="flex gap-5 justify-end">
                    <div class="@if($membersCount <= $record->aircraft->number_of_person) badge-success @else badge-danger @endif">Létszám: {{ $membersCount }} / {{ $record->aircraft->number_of_person }}</div>
                    <div class="@if($bodiesWeight <= $record->aircraft->payload_capacity) badge-success @else badge-danger @endif">Súly: {{ $bodiesWeight }} / {{ $record->aircraft->payload_capacity }} kg</div>
                </div>
                <div class="grid grid-cols-2 my-4 ">
                    <h3 class="fi-header-heading text-sm tracking-tight text-gray-600 dark:text-white sm:text-sm my-4 col-span-full"><b>Légijármű leírása:</b><br>{{ $record->aircraft->description }}</h3>
                    <div>
                        <h3 class="fi-header-heading text-sm tracking-tight text-gray-600 dark:text-white sm:text-sm"><b>Publikus megjegyzés:</b><br>{{ $record->public_description }}</h3>
                    </div>
                    <div>
                        <h3 class="fi-header-heading text-sm tracking-tight text-gray-600 dark:text-white sm:text-sm"><b>NEM publikus megjegyzés:</b><br>{{ $record->non_public_description }}</h3>
                    </div>
                </div>
            </div>
        </header>

        @php
            $columns = 8;
            $grid_cols = '3.5rem';
            for ($i=1; $i < $columns; $i++) { 
                $grid_cols .= ' auto';
            }
            $style = 'grid-column: span '. $columns-1 .' / span '. $columns-1 .';';
        @endphp
        <div class="grid overflow-auto custom-table rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            style="grid-template-columns: {{ $grid_cols }};">

            <div class="thead"></div>
            <div class="thead">Kupon kód</div>
            <div class="thead">Kapcsolattartó</div>
            <div class="thead">Jelentkezett ekkor</div>
            <div class="thead">Jegytípus</div>
            <div class="thead">Aktív/Inaktív jelentkezések</div>
            <div class="thead">Fő</div>
            <div class="thead">Súly</div>

            @foreach ($record->coupons as $coupon)
                @php
                    $isCheckedAlready = in_array($coupon->id, $alreadyCheckedCoupons);

                    // Sötét háttérszín esetén fehér lesz a szöveg színe, világos háttérnél pedig fekete.
                    $rgb = list($red, $green, $blue) = sscanf($coupon->tickettype->color, "#%02x%02x%02x");
                    $backgroundColor = 'rgb(' . implode(', ', $rgb) . ')';
                    if (($red * 0.299 + $green * 0.587 + $blue * 0.114) > 186) {
                        $textColor = 'black';
                    } else {
                        $textColor = 'white';
                    }
                @endphp
                <label id="checkbox" class="tbody @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif>
                    <input id="coupon-{{ $coupon->id }}" class="checkbox ms-2" type="checkbox" @disabled($isCheckedAlready || $coupon->missingData) wire:model.live="selectedCoupons" value="{{ $coupon->id }}">
                </label>
                <label for="coupon-{{ $coupon->id }}" class="tbody min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif><span style="opacity: 1">{{ $coupon->coupon_code }}</span></label>
                <label for="coupon-{{ $coupon->id }}" class="tbody min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif><span style="opacity: 1">{{ $coupon->user->name }}</span></label>
                <label for="coupon-{{ $coupon->id }}" class="tbody min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif><span style="opacity: 1">{{ Carbon\Carbon::parse($coupon->pivot->created_at)->translatedFormat('Y.m.d., H:i') }}</span></label>
                <label for="coupon-{{ $coupon->id }}" class="tbody min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif><span style="opacity: 1">{{ $coupon->tickettype->name }}</span></label>
                <label for="coupon-{{ $coupon->id }}" class="tbody min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif><span style="opacity: 1">{{ $coupon->aircraftLocationPilots->where('pivot.status', 0)->where('date', '>=', now())->count() }}/{{ $coupon->aircraftLocationPilots->where('pivot.status', 0)->where('date', '<', now())->count() }}</span></label>
                <label for="coupon-{{ $coupon->id }}" class="tbody min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif><span style="opacity: 1">{{ $coupon->membersCount }}</span></label>
                <label for="coupon-{{ $coupon->id }}" class="tbody min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif><span style="opacity: 1">{{ $coupon->membersBodyWeight }} kg</span></label>

                @foreach ($coupon->childrenCoupons->where('source', 'Kiegészítő') as $item)
                    @if($item->description || $item->total_price)
                        <label for="coupon-{{ $coupon->id }}" class="description min-w-1 @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif></label>
                        <label for="coupon-{{ $coupon->id }}" class="description w-full  @if($isCheckedAlready || $coupon->missingData) bg-zinc-100 text-zinc-400 dark:bg-white/10" style='{{ $style }}' @else " style="{{ $style }} background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif>
                            @if($item->description)
                                <span style="opacity: 1">Megjegyzés: {{ $item->description }}</span>
                            @endif
                            @if ($item->total_price)
                                <span style="opacity: 1">Ár: {{ $item->total_price }} Ft</span>
                            @endif
                        </label>
                    @endif
                @endforeach
            @endforeach
        </div>

    
</div>
</x-filament-panels::page>
