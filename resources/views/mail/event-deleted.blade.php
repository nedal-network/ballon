<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        <p>A repülés mely számodra véglegesített volt törlésre került időjárás, vagy minimum létszám nem teljesülése miatt, így ez már nem élő időpont, erre az alkalomra ne készülj. A kellemetlenséget sajnáljuk.</p>
        <br>
        <p>
            <strong>Törölt repülés adatai: </strong><br>
            <strong>Légijármű típus, azonosító: </strong>{{ $event->aircraft->name }}, {{ $event->aircraft->registration_number }}<br>
            <strong>Régió: </strong>{{ $event->region->name }}<br>
            @php
                $dateTime = Carbon\Carbon::parse("{$event->date} {$event->time}");
            @endphp
            <strong>Dátum: </strong>{{ $dateTime->format('Y.m.d.') }}, {{ ucfirst($dateTime->translatedFormat('l')) }} <strong>Tervezett találkozási időpont: </strong>{{ $dateTime->format('H:i') }}<br>
            <strong>Helyszín: </strong><a href="{{ $event->location->online_map_link }}">{{ $event->location->name }}, {{ $event->location->zip_code }} {{ $event->location->settlement }}, {{ $event->location->address }} {{ $event->location->address_number }}</a>
        </p>
    </div>
</x-mail::message>
