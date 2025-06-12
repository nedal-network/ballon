<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        @php
            $childrenCoupons = $coupon->childrenCoupons()->withoutGlobalScopes();
        @endphp
        <p>Eljött a várva várt idő!🙂 Véglegesítettük az alábbi repülést, melyre beválogattunk a(z)</p>
        <p><strong>{{ $coupon->coupon_code }}</strong> kódú kuponoddal,</p>
        <p>{{ $coupon->adult + ($childrenCoupons?->sum('adult') ?? 0) }} felnőtt + {{ $coupon->children + ($childrenCoupons?->sum('children') ?? 0) }} gyerek, létszámmal,</p>
        <p>{{ $coupon->membersBodyWeight }} kg utas össztömeggel tervezve, melynél</p>
        @php
            $virtualChildrenCoupons = $coupon->childrenCoupons()->withoutGlobalScopes()->where('source', 'Kiegészítő')->whereNotNull('total_price');
        @endphp
        <p>{{ number_format($virtualChildrenCoupons->sum('total_price'), 0, ',', ' ') }} Ft a helyszínen készpénzben fizetendő.</p>
        <br>
        <p>
            <strong>Repülés adatai</strong>
            <strong>Légijármű típus, azonosító: </strong>{{ $event->aircraft->name }}, {{ $event->aircraft->registration_number }}<br>
            <strong>Régió: </strong>{{ $event->region->name }}<br>
            @php
                $dateTime = Carbon\Carbon::parse("{$event->date} {$event->time}");
            @endphp
            <strong>Dátum: </strong>{{ $dateTime->format('Y.m.d.') }}, {{ ucfirst($dateTime->translatedFormat('l')) }} <strong>Tervezett találkozási időpont: </strong>{{ $dateTime->format('H:i') }}<br>
            <strong>Helyszín: </strong><a href="{{ $event->location->online_map_link }}">{{ $event->location->name }}, {{ $event->location->zip_code }} {{ $event->location->settlement }}, {{ $event->location->address }} {{ $event->location->address_number }}</a><br>
            <strong>Megközelítés: </strong><img src="{{ asset('storage/' . $event->location->image_path) }}" alt="{{ $event->location->name }} megközelítési kép"><br>
            @php
                $times = [
                    '00:30:00' => 'fél óra',
                    '01:00:00' => '1 óra',
                    '01:30:00' => '1 és fél óra',
                    '02:00:00' => '2 óra',
                    '02:30:00' => '2 és fél óra',
                    '03:00:00' => '3 óra',
                    '03:30:00' => '3 és fél óra',
                    '04:00:00' => '4 óra',
                    '04:30:00' => '4 és fél óra',
                    '05:00:00' => '5 óra',
                    '05:30:00' => '5 és fél óra',
                    '06:00:00' => '6 óra',
                ];
            @endphp
            <strong>Program várható időtartama: </strong>{{ $times[$event->period_of_time] }}
            @if (filled($event->location->description))
                <br>
                <strong>Helyszínnel kapcsolatos megjegyzés: </strong>{{ $event->location->description }}
            @endif
        </p>
        <p><strong>Fontos: </strong>Ha ez az időpont mégsem lesz jó számodra, akkor minél hamarabb jelentkezz le, mivel a lejelentkezési határidő letelte után ezt már nem tudod megtenni a kuponod elvesztése nélkül. Szolgáltatótól függően ez tipikusan 1-4 hét között változik. Kivételt csak alátámasztott egészségügyi, vagy egyéb igen indokolt eset képez.
        <p>Kérlek, vedd figyelembe, hogy az időponton +-30 percet és a helyszínen is változtatunk a repülés előtti napokban, ha ezt az időjárás előrejelzés alapján szükségesnek látjuk. Ha ezt megtesszük, arról e-mail, vagy sms értesítést fogsz kapni.
        <p>A helyszínre a kijutást neked kell egyénileg megoldani a változtatást követően is, illetve javasoljuk, hogy a helyszínnel kapcsolatban részletesen tájékozódj a weboldalon és érdeklődj, ha esetleg nem vagy biztos benne, hogy megtalálod. A repülés előtt már nincs kapacitásunk téged útba igazítani, ha akkor derül ki, hogy a címet nem találod meg és így lemaradsz a repülésről.
        <p>Egy repülés lemondáskor minden résztvevőnek egyszerre kerül kiküldésre a lemondásról szóló üzenet. Amég ez nem történik meg, addig a repülés végleges.
        <p>
            A repülési előtti órákban az ottani csapatot (jellemzően a pilótát) az alábbi elérhetőségen éred el szükség esetén elsődlegesen sms-ben. (két repülés között a pilóták jellemzően pihenő idejüket töltik és nem veszik fel a telefont) {{ $event->pilot->fullname }}, {{ $event->pilot->phone }}
            <br>
            Kérünk, hogy ha szerinted aznap nem tűnik jónak az időjárás a programhoz, akkor a repülés esélyeivel kapcsolatban NE keresd a pilótát, mivel erről nem fog neked semmilyen információt adni. Egy repülés lemondáskor minden résztvevőnek egyszerre kerül kiküldésre a lemondásról szóló üzenet. Amég ez nem történik meg, addig a repülés végleges.
        </p>
        <p>Kérjük, ne feledkezz el a személyenkénti kitöltött felelősség vállalási nyilatkozatról, továbbá tájékozódj a repülési feltételekről, ruházati követelményekről is, ha esetleg ezt az információt nem ismerted meg.</p>
        <br>
        <p style="text-align: center;"><strong>Kérjük jelezz vissza nekünk, ha megkaptad az üzenetet, a gomb megnyomásával</strong></p>
        <div align="center" style="text-align: center;">
            <a class="btn accent" href="{{ $confirmationLink }}">Visszajelzés</a>
        </div>
        <br>
        <p style="text-align: center;">vagy válasz üzenetben az <a href="mailto:info@utasfoglalo.hu">info@utasfoglalo.hu</a> címre.</p>
    </div>
</x-mail::message>