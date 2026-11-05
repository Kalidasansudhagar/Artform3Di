{{-- Closing call to action. Tones: "light" (warm white), "dark" (charcoal), "steel" (drone engineering). --}}
@props([
    'tone' => 'light',
    'eyebrow' => 'Start a conversation',
    'heading' => 'Have an Idea? <em>Let’s Create It.</em>',
    'text' => 'From custom sculptures to innovative engineering solutions, let’s bring your ideas to life.',
])

@php $light = $tone === 'light'; @endphp

<section {{ $attributes->class([
    'relative overflow-hidden',
    'bg-bone text-ink' => $light,
    'bg-coal text-bone' => $tone === 'dark',
    'bg-deep text-bone blueprint' => $tone === 'steel',
]) }}>
    {{-- Layered pyramid motif echoing the logo --}}
    <svg aria-hidden="true" viewBox="0 0 600 520" class="pointer-events-none absolute -bottom-24 left-1/2 w-[900px] max-w-none -translate-x-1/2 opacity-[0.04] lg:w-[1200px]" fill="currentColor">
        @for ($i = 0; $i < 26; $i++)
            <rect x="{{ 300 - 11 * ($i + 1) }}" y="{{ 10 + $i * 20 }}" width="{{ 22 * ($i + 1) }}" height="7" rx="3.5" />
        @endfor
    </svg>

    <div class="container-x relative py-28 text-center md:py-40 lg:py-48">
        <p @class(['eyebrow eyebrow-plain justify-center', 'text-smoke' => $light, 'text-brass' => ! $light])>{{ $eyebrow }}</p>
        <h2 class="display mx-auto mt-8 max-w-5xl text-display-2" data-split>{!! $heading !!}</h2>
        <p @class(['lead mx-auto mt-8 max-w-xl', 'text-ink/65' => $light, 'text-bone/65' => ! $light]) data-reveal>{{ $text }}</p>
        <div class="mt-12 flex flex-col items-center justify-center gap-3 sm:flex-row" data-reveal>
            <x-button :href="route('contact').'#enquiry'" :variant="$light ? 'dark' : 'light'" size="lg">Start Your Project</x-button>
            <x-button :href="route('contact')" :variant="$light ? 'outline-dark' : 'outline-light'" size="lg" icon="arrow-right">Contact Us</x-button>
        </div>
    </div>
</section>
