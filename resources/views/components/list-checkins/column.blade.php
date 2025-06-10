@props([
    'coupon' => null,
    'disabled' => true,
    'backgroundColor' => null,
    'textColor' => null,
])
<label 
    wire:loading.class="cursor-wait" 
    for="coupon-{{ $coupon->id }}"
    @class([
        $attributes->merge(['class' => 'tbody min-w-min text-sm'])['class'],
        'bg-zinc-100 text-zinc-400 dark:bg-white/10' => $disabled,
    ])
    @if (!$disabled) style="background: {{ $backgroundColor }}; color: {{ $textColor }}" @endif
>
    <span style="opacity: 1">{{ $slot }}</span>
</label>