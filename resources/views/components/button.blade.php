{{--
    Variants: light, dark, brass, outline-light, outline-dark.
    Renders an <a> when href is given, otherwise a <button>.
--}}
@props(['href' => null, 'variant' => 'light', 'size' => null, 'icon' => 'arrow-up-right'])

@php
    $classes = ['btn', "btn-{$variant}", $size ? "btn-{$size}" : null];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        <span>{{ $slot }}</span>
        @if ($icon)<span class="btn-icon"><x-icon :name="$icon" /></span>@endif
    </a>
@else
    <button type="{{ $attributes->get('type', 'button') }}" {{ $attributes->except('type')->class($classes) }}>
        <span>{{ $slot }}</span>
        @if ($icon)<span class="btn-icon"><x-icon :name="$icon" /></span>@endif
    </button>
@endif
