<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        @php
            $childrenCoupons = $coupon->childrenCoupons()->withoutGlobalScopes();
        @endphp
        <p>
            A(z) <strong>{{ $coupon->coupon_code }}</strong> kódú kuponod jóváhagyásra került az alábbi adatokkal:<br>
            <strong>Jegytípus: </strong>{{ $coupon->tickettype->name }}  /  {{ $coupon->tickettype->aircrafttype->getLabel() }}<br>
            <strong>Lejárat: </strong>{{ Carbon\Carbon::parse($coupon->expiration_at)->format('Y.m.d') }}<br>
            <strong>Létszám: </strong>{{ $coupon->adult + ($childrenCoupons?->sum('adult') ?? 0) }} felnőtt + {{ $coupon->children + ($childrenCoupons?->sum('children') ?? 0) }} gyerek<br>
            @php
                $virtualChildrenCoupons = $coupon->childrenCoupons()->withoutGlobalScopes()->where('source', 'Kiegészítő')->whereNotNull('total_price');
            @endphp
            <strong>Helyszínen fizetendő összeg:</strong> {{ number_format(($virtualChildrenCoupons?->sum('total_price') ?? 0), 0, ',', ' ') }} Ft
        </p>
        <p>Addig is élményekkel teli felhasználást és repülést kívánunk.</p>
    </div>
</x-mail::message>
