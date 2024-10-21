<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        <p>
            A repülésről, mely számodra már véglegesített volt vagy lejelentkeztettünk, vagy te jelentkeztél le, így a továbbiakban ez már nem élő számodra.</p>
            <strong>Repülés adatai, melyről lekerültél:</strong><br>
            <strong>Légijármű típus, azonosító: </strong>{{ $event->aircraft->name }}, {{ $event->aircraft->registration_number }}<br>
            <strong>Régió: </strong>{{ $event->region->name }}<br>
            @php
                $dateTime = Carbon\Carbon::parse("{$event->date} {$event->time}");
            @endphp
            <strong>Dátum: </strong>{{ $dateTime->format('Y.m.d') }}, {{ ucfirst($dateTime->translatedFormat('l')) }} <strong>Tervezett találkozási időpont: </strong>{{ $dateTime->format('H:i') }}<br>
            <strong>Helyszín: </strong><a href="{{ $event->location->online_map_link }}">{{ $event->location->name }}, {{ $event->location->zip_code }} {{ $event->location->settlement }}, {{ $event->location->address }} {{ $event->location->address_number }}</a><br>
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
        </p>
    </div>
</x-mail::message>
