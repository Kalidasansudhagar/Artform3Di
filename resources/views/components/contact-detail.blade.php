{{-- Renders a configured contact value, or a clearly marked placeholder until the client confirms it. --}}
@props(['key', 'label'])

@php
    $value = config("artform.contact.{$key}");
    $href = match (true) {
        ! $value => null,
        $key === 'email' => 'mailto:'.$value,
        $key === 'phone' => 'tel:'.preg_replace('/[^\d+]/', '', $value),
        default => null,
    };
@endphp

<span {{ $attributes }}>
    @if ($href)
        <a href="{{ $href }}" class="link-line">{{ $value }}</a>
    @elseif ($value)
        {{ $value }}
    @else
        <span class="italic opacity-55">{{ $label }} to be confirmed</span>
    @endif
</span>
