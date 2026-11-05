@props(['number' => null])

<p {{ $attributes->class('eyebrow') }}>
    @if ($number)<span class="font-mono tracking-[0.12em] opacity-60">{{ $number }}</span>@endif
    <span>{{ $slot }}</span>
</p>
