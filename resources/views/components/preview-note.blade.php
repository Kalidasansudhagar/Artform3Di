{{-- Honest notice shown on account screens while authentication is not yet connected. --}}
<p {{ $attributes->class('flex items-start gap-3 bg-bone px-4 py-3 text-xs leading-relaxed text-ink/65') }}>
    <x-icon name="shield-check" class="mt-0.5 shrink-0 text-sm text-brass" />
    <span>{{ $slot->isEmpty() ? 'Preview — customer accounts launch with online ordering. Nothing you enter here is sent or stored.' : $slot }}</span>
</p>
