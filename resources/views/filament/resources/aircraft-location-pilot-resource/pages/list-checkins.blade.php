<x-filament-panels::page>
    <div class="flex h-[calc(100vh-64px)] flex-col gap-y-8 py-8">
        <header>
            <div class="grid grid-cols-2">
                <div>
                    @php
                        $subHeading = [];
                        $record->aircraft?->name && ($subHeading[] = $record->aircraft->name);
                        $record->region?->name && ($subHeading[] = $record->region->name);
                        $record->location?->name && ($subHeading[] = $record->location->name);
                    @endphp
                    <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ Carbon\Carbon::parse($record->date . ' ' . $record->time)->translatedFormat('Y.m.d., H:i') }}</h1>
                    <h2 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">{{ implode(', ', $subHeading) }}</h2>

                </div>
                @php
                    $bodiesWeight = 0;
                    $membersCount = 0;
                    foreach ($record->coupons->whereIn('id', $this->selectedCoupons) as $coupon) {
                        $bodiesWeight += $coupon->membersBodyWeight;
                        $membersCount += $coupon->membersCount;
                    }
                @endphp
                <div class="flex justify-end gap-5">
                    <div class="@if ($membersCount <= $record->aircraft->number_of_person) badge-success @else badge-danger @endif">Létszám: {{ $membersCount }} / {{ $record->aircraft->number_of_person }}</div>
                    <div class="@if ($bodiesWeight <= $record->aircraft->payload_capacity) badge-success @else badge-danger @endif">Súly: {{ $bodiesWeight }} / {{ $record->aircraft->payload_capacity }} kg</div>
                </div>
                <div class="my-4 grid grid-cols-2">
                    <h3 class="fi-header-heading col-span-full my-4 text-sm tracking-tight text-gray-600 dark:text-white sm:text-sm"><b>Légijármű leírása:</b><br>{{ $record->aircraft->description }}</h3>
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
            $columns = 12;
            $grid_cols = '0.75rem';
            for ($i = 1; $i < $columns; $i++) {
                $grid_cols .= ' auto';
            }
            $style = 'grid-column: span ' . $columns - 1 . ' / span ' . $columns - 1 . ';';
        @endphp

        <div class="flex gap-4 h-min items-center">
            @php
                $selectedCoupons = $record->coupons->whereIn('id', $this->selectedCoupons);
                $selectedContacts = $selectedCoupons->map(fn ($coupon) => $coupon->user)->flatten();
            @endphp
            <h4>Kijelölt utasok adatainak másolás:</h4>
            <x-filament::button x-data="" @click="navigator.clipboard.writeText('{{ $selectedContacts->where('email', '!=', '')->implode('email', ';') }}'); new FilamentNotification().title('Email címek vágólapra másolva').icon('tabler-mail-forward').success().send()">
                <x-tabler-mail-forward class="h-5" />
            </x-filament::button>

            <x-filament::button x-data="" @click="navigator.clipboard.writeText('{{ $selectedContacts->where('phone', '!=', '')->implode('phone', ';') }}'); new FilamentNotification().title('Telefonszámok vágólapra másolva').icon('tabler-device-mobile').success().send()">
                <x-tabler-device-mobile class="h-5" />
            </x-filament::button>
        </div>
        <div x-data wire:loading.class="cursor-wait opacity-70" class="transition-all custom-table grid overflow-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="grid-template-columns: {{ $grid_cols }};">

            <div class="thead text-sm" style="padding: 0 !important;"></div>
            <div class="thead text-sm"></div>
            <div class="thead text-sm">Kupon kód</div>
            <div class="thead text-sm">Kapcsolattartó</div>
            <div class="thead text-sm">E-mail</div>
            <div class="thead text-sm">Telefonszám</div>
            <div class="thead text-sm">Jelentkezett ekkor</div>
            <div class="thead text-sm">Lejárati dátum</div>
            <div class="thead text-sm">Jegytípus</div>
            <div class="thead text-sm">A/I</div>
            <div class="thead text-sm">Fő</div>
            <div class="thead text-sm">Súly</div>

            @foreach ($record->coupons as $coupon)
                @php
                    $isCheckedAlready = in_array($coupon->id, $alreadyCheckedCoupons);

                    // Sötét háttérszín esetén fehér lesz a szöveg színe, világos háttérnél pedig fekete.
                    $rgb = [$red, $green, $blue] = sscanf($coupon->tickettype->color, '#%02x%02x%02x');
                    $backgroundColor = 'rgb(' . implode(', ', $rgb) . ')';
                    if ($red * 0.299 + $green * 0.587 + $blue * 0.114 > 186) {
                        $textColor = 'black';
                    } else {
                        $textColor = 'white';
                    }

                    // A kupon olyan eseményei, ami jövőbeli
                    $actives = $coupon->aircraftLocationPilots->filter(
                        fn ($event) => Carbon\Carbon::parse($event->date) >= now()
                    );

                    // A kupon olyan eseményei, amire nem lett beválogatva és az már lezajlott, vagy törölve lett
                    $inactives = $coupon->aircraftLocationPilots->filter(
                        fn ($event) => $event->pivot->status == 0 && (Carbon\Carbon::parse($event->date) < now() || in_array($event->status, [\App\Enums\AircraftLocationPilotStatus::Deleted, \App\Enums\AircraftLocationPilotStatus::Feedback, \App\Enums\AircraftLocationPilotStatus::Executed]))
                    );

                    $isConfirmed = in_array($coupon->id, $this->selectedCoupons) && in_array($coupon->id, $this->confirmedCoupons);

                    $confirmedMessage = match(true) {
                        ! in_array($coupon->id, $this->selectedCoupons) => 'Nincs beválogatva',
                        $isConfirmed => 'Megerősítve',
                        default => 'Megerősítésre vár',
                    };

                    $disabled = $isCheckedAlready || $coupon->missingData || $record->status === \App\Enums\AircraftLocationPilotStatus::Deleted;
                @endphp
                <div class="tbody w-full h-full" 
                    title="{{ $confirmedMessage }}"
                    wire:click="toggleConfirmation({{ $coupon->id }})" 
                    @disabled($disabled || ! in_array($coupon->id, $this->selectedCoupons)) 
                    @style([
                        'background-color: lightgray; padding: 0 !important;',
                        'background-color: rgb(234 179 8);' => in_array($coupon->id, $this->selectedCoupons) && $coupon->status === \App\Enums\CouponStatus::Applicant,
                        'background-color: rgb(34 197 94);' => in_array($coupon->id, $this->selectedCoupons) && in_array($coupon->id, $this->confirmedCoupons),
                    ])
                ></div>
                <label wire:loading.class="cursor-wait" id="checkbox" class="tbody @if ($disabled) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif>
                    <input wire:loading.class="cursor-wait" wire:loading.attr="disabled" id="coupon-{{ $coupon->id }}" class="checkbox ms-2" type="checkbox" @disabled($disabled) wire:model.live="selectedCoupons" value="{{ $coupon->id }}">
                </label>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ $coupon->coupon_code }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ $coupon->user->name }} {{ $coupon->user->deleted_at ? '(törölve)' : '' }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>
                    <span class="group/item">
                        <a class="flex gap-2 group-hover/item:underline group-focus-visible/item:underline" x-data @click.stop href="mailto:{{ $coupon->user->email }}">
                            @svg('heroicon-m-envelope', ['class' => 'h-5 w-5']) {{ $coupon->user->email }}
                        </a>
                    </span>
                </x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ $coupon->user->phone }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ Carbon\Carbon::parse($coupon->pivot->created_at)->translatedFormat('Y.m.d., H:i') }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ Carbon\Carbon::parse($coupon->expiration_at)->translatedFormat('Y.m.d.') }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ $coupon->tickettype->name }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ $actives->count() }}/{{ $inactives->count() }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ $coupon->membersCount }}</x-list-checkins.column>
                <x-list-checkins.column :$coupon :$disabled :$backgroundColor :$textColor>{{ $coupon->membersBodyWeight }} kg</x-list-checkins.column>
                @php
                    $summ_price = 0;
                @endphp
                <label wire:loading.class="cursor-wait" for="coupon-{{ $coupon->id }}" class="text-xs min-w-min @if ($disabled) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif></label>
                <label wire:loading.class="cursor-wait" for="coupon-{{ $coupon->id }}" class="text-xs @if ($disabled) bg-zinc-100 text-zinc-400 dark:bg-white/10" style='{{ $style }} padding-bottom: 0px !important;' @else " style="{{ $style }} background: {{ $backgroundColor }}; color: {{ $textColor }}; padding-bottom: 0px !important;" @endif>
                    <div> 
                        @foreach ($coupon->childrenCoupons->where('source', 'Kiegészítő') as $item) 
                            @php
                                $extra_coupon = [];
                                if ($item->description) {
                                    $extra_coupon[] = 'Megjegyzés: ' . $item->description;
                                }
                                if ($item->total_price) {
                                    $summ_price += $item->total_price;
                                    $extra_coupon[] = 'Ár: ' . number_format($item->total_price, 0, '', ' ') . ' Ft';
                                }
                                echo (!empty($extra_coupon) && !$loop->first ? ' ' : '') . implode(', ', $extra_coupon);
                            @endphp 
                        @endforeach
                    </div>
                </label> 
                @if ($summ_price) 
                    <label wire:loading.class="cursor-wait" for="coupon-{{ $coupon->id }}" class="w-full text-xs min-w-min pb-2 @if ($disabled) bg-zinc-100 text-zinc-400 dark:bg-white/10" @else " style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif></label>
                    <label wire:loading.class="cursor-wait" for="coupon-{{ $coupon->id }}" class="text-xs pb-2 @if ($disabled) bg-zinc-100 text-zinc-400 dark:bg-white/10" style='{{ $style }}' @else " style="{{ $style }} background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif>
                        <span class="w-full font-bold">Helyszínen fizetendő: {{ number_format($summ_price, 0, '', ' ') }} Ft</span>
                    </label>
                @endif
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
