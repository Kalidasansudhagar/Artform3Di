{{-- Social profiles fall back to inert placeholders until URLs are configured. --}}
<ul {{ $attributes->class('flex flex-wrap gap-x-6 gap-y-3') }}>
    @foreach (config('artform.social') as $network => $url)
        <li>
            @if ($url)
                <a href="{{ $url }}" target="_blank" rel="noopener" class="link-line text-sm">{{ $network }}<x-icon name="arrow-up-right" class="text-xs" /></a>
            @else
                <span class="inline-flex items-center gap-2 text-sm opacity-50" title="{{ $network }} profile to be added">{{ $network }}<x-icon name="arrow-up-right" class="text-xs" /></span>
            @endif
        </li>
    @endforeach
</ul>
