{{--
    Horizontal single-hue bars with the value at the tip. Items: ['label' => .., 'value' or 'count' => .., 'tone' => ?].
    A tone adds a status badge in place of the plain label.
--}}
@props(['items', 'currency' => false])

@php $max = max(array_map(fn ($item) => $item['value'] ?? $item['count'], $items)) ?: 1; @endphp

<ul {{ $attributes->class('space-y-4') }}>
    @foreach ($items as $item)
        @php
            $value = $item['value'] ?? $item['count'];
            $display = $currency ? '₹'.number_format($value) : number_format($value);
        @endphp
        <li class="group" title="{{ $item['label'] }}: {{ $display }}">
            <div class="flex items-center justify-between gap-3 text-sm">
                @if (isset($item['tone']))
                    <x-admin.status-badge :tone="$item['tone']" :label="$item['label']" />
                @else
                    <span class="truncate text-ink/80">{{ $item['label'] }}</span>
                @endif
                <span class="font-semibold tabular-nums">{{ $display }}</span>
            </div>
            <div class="mt-2 h-2 rounded-full bg-ink/[0.05]">
                <div class="h-2 rounded-full bg-[#9a6f24] transition-[filter] group-hover:brightness-110" style="width: {{ max(2, round($value / $max * 100, 1)) }}%"></div>
            </div>
        </li>
    @endforeach
</ul>
