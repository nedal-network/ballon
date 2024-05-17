<x-mail::message>
    <h3 class="greeting">
        Kedves {{ $user->name }}!
    </h3>
    <div class="content">
        <p>A(z) {{ $coupon->coupon_code }} kódú kuponod jóváhagytuk.</p>
    </div>
</x-mail::message>
