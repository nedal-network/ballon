<x-mail::message>
    <div class="content">
        <p>
            A(z) <strong>{{ $coupon->coupon_code }}</strong> kódú kupon <strong>jóváhagyásra vár</strong> az alábbi adatokkal:<br>
            <strong>Jegytípus: </strong>{{ $coupon->tickettype?->name ?? 'Ismeretlen jegytípus' }} / {{ $coupon->tickettype?->aircrafttype->getLabel() ?? 'Ismeretlen légijárműtípus' }}<br>
            <strong>Lejárat: </strong>{{ Carbon\Carbon::parse($coupon->expiration_at)->format('Y.m.d.') }}<br>
            <strong>Létszám: </strong>{{ $coupon->adult }} felnőtt + {{ $coupon->children }} gyerek
        </p>
    </div>
</x-mail::message>
