<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        <p>Az alábbi repülésre jelentkeztél fel a(z) <strong>{{ $coupon->coupon_code }}</strong> kódú kuponoddal.</p>
        <p>
            <strong>Légijármű típus, azonosító: </strong>{{ $event->aircraft->name }}, {{ $event->aircraft->registration_number }}<br>
            <strong>Régió: </strong>{{ $event->region->name }}<br>
            @php
                $dateTime = Carbon\Carbon::parse("{$event->date} {$event->time}");
            @endphp
            <strong>Dátum: </strong>{{ $dateTime->format('Y.m.d.') }}, {{ ucfirst($dateTime->translatedFormat('l')) }} <strong>Tervezett találkozási időpont: </strong>{{ $dateTime->format('H:i') }}
        </p>
        <p>Kérlek, vedd figyelembe, hogy a találkozási pont a repülés véglegesítésekor kerül meghatározásra, illetve mint a helyszínen, mind a pontos találkozási időponton változtatunk a repülés előtti napokban, ha ezt az időjárás előrejelzés alapján szükségesnek látjuk.</p>
        <p><strong>Fontos: </strong>Ha ez az időpont mégsem lesz jó számodra, akkor minél hamarabb jelentkezz le, mivel ha beválogatunk egy repülésre és a lejelentkezési határidő után tennéd ezt meg, akkor ez a kuponod elvesztésével/beváltásával jár, mintha repültél volna.</p>
        <p>Ha erre a repülésre beválogatunk, akkor arról véglegesített repülés tartalommal fogsz levelet kapni. Ez az üzenet csak visszaigazolás a jelentkezésedről egy időpontra, nem az időpontod véglegesítése.</p>
    </div>
</x-mail::message>
