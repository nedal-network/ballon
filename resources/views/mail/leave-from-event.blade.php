<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        <p>Az alábbi repülésről jelentkeztél le a(z) <strong>{{ $coupon->coupon_code }}</strong> kódú kuponoddal.</p>
        <p>
            <strong>Légijármű típus, azonosító: </strong>{{ $event->aircraft->name }}, {{ $event->aircraft->registration_number }}<br>
            <strong>Régió: </strong>{{ $event->region->name }}<br>
            @php
                $dateTime = Carbon\Carbon::parse("{$event->date} {$event->time}");
            @endphp
            <strong>Dátum: </strong>{{ $dateTime->format('Y.m.d') }}, {{ ucfirst($dateTime->translatedFormat('l')) }} <strong>Tervezett találkozási időpont: </strong>{{ $dateTime->format('H:i') }}
        </p>
    </div>
</x-mail::message>
