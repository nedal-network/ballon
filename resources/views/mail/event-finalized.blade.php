<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        <p>Véglegesítettük az időpontot, amire jelentkeztél így jössz velünk repülni.</p>
        <br>
        <p>
            Repülés részletei:
            <br>
            <strong>Helyszín:</strong> {{ $event->region->name . ', ' . $event->location->name }}<br>
            <strong>Időpont:</strong> {{ $event->dateTime }}
        </p>
    </div>
</x-mail::message>
