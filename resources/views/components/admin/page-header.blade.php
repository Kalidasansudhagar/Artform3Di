{{-- Admin page heading with optional actions on the right. --}}
@props(['title', 'description' => null])

<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h2 class="display text-4xl">{{ $title }}</h2>
        @if ($description)<p class="mt-2 text-sm text-ink/60">{{ $description }}</p>@endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex shrink-0 gap-2">{{ $slot }}</div>
    @endif
</div>
