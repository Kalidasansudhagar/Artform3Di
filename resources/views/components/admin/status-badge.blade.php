{{-- Order/enquiry status. Status colours are reserved for state and always paired with an icon + label. --}}
@props(['tone' => 'neutral', 'label'])

@php
    [$icon, $bg, $fg] = match ($tone) {
        'good' => ['circle-check', 'bg-[#2f8350]/10', 'text-[#2f8350]'],
        'warning' => ['clock', 'bg-[#c08417]/12', 'text-[#c08417]'],
        'critical' => ['circle-x', 'bg-[#b5432f]/10', 'text-[#b5432f]'],
        default => ['circle-dashed', 'bg-ink/[0.06]', 'text-smoke'],
    };
@endphp

<span {{ $attributes->class(["inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap text-ink {$bg}"]) }}>
    <x-icon :name="$icon" class="text-[13px] {{ $fg }}" />{{ $label }}
</span>
