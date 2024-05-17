<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $passenger->fullname }}!
    </h3>
    <div class="content">
        <p>Az alábbi repülést sikeresen végrehajtottuk.</p>
        <br>
        <p>
            Repülés részletei:
            <br>
            <strong>Helyszín:</strong> {{ $event->region->name . ', ' . $event->location->name }}<br>
            <strong>Időpont:</strong> {{ $event->dateTime }}
        </p>
        <br>
        <p>Köszönjük, hogy velünk repültél!</p>
    </div>
</x-mail::message>
