<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        <p>
            A(z) <strong>{{ $coupon->coupon_code }}</strong> kódú kuponod lejárt.<br>
            <strong>Lejárati dátum: </strong>{{ Carbon\Carbon::parse($coupon->expiration_at)->translatedFormat('Y.m.d') }}
        </p>
        <p>Ezzel a kuponnal már nem fogsz tudni feljelentkezni a repülésekre és körüzeneteket sem fogsz kapni, ha nem maradt érvényes kuponod.</p>
        <p>Ha szeretnél hosszabbítani, vagy úgy gondolod, hibás a lejárat, írj nekünk.</p>
    </div>
</x-mail::message>
