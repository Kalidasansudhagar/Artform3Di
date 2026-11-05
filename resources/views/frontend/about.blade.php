@extends('layouts.app')

@section('title', 'About Us')
@section('description', 'ARTFORM 3Di brings together art, technology and engineering to create premium 3D-printed sculpture, products and drone platforms.')
@section('header_theme', 'light')

{{--
    Draft copy for client review. The team section is intentionally omitted until
    the client supplies names, roles and portraits — pass a $team array to enable it.
--}}

@section('content')

{{-- Brand introduction --}}
<section class="overflow-x-clip bg-paper pt-[calc(var(--header-h)+5rem)] lg:pt-[calc(var(--header-h)+7rem)]">
    <div class="container-x">
        <x-eyebrow class="text-smoke" data-reveal>About ARTFORM 3Di</x-eyebrow>
        <h1 class="display mt-10 text-display-1" data-split data-split-immediate data-delay="0.2">
            We shape ideas <br class="hidden md:block"><em>into objects</em> <span class="text-smoke">that last.</span>
        </h1>
        <div class="mt-14 grid gap-8 lg:grid-cols-12" data-reveal data-delay="0.7">
            <p class="lead text-ink/70 lg:col-span-5 lg:col-start-8">
                ARTFORM 3Di is a creative studio and engineering workshop. We bring artistry and advanced 3D printing together to make sculpture, products and machines with character and precision.
            </p>
        </div>
    </div>
    <div class="mt-20 lg:mt-28">
        <div class="media-frame aspect-[16/10] w-full sm:aspect-[16/8]" data-reveal-image>
            <img src="{{ asset('assets/images/about/studio.svg') }}" alt="The studio’s 3D printer at work" width="1600" height="1100" loading="eager" decoding="async" class="scale-115" data-parallax="0.12">
        </div>
    </div>
</section>

{{-- Company story --}}
<section class="section bg-paper">
    <div class="container-x grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-3">
            <div class="lg:sticky lg:top-32">
                <x-eyebrow number="01" class="text-smoke">Our Story</x-eyebrow>
            </div>
        </div>
        <div class="space-y-8 text-lg leading-relaxed text-ink/70 lg:col-span-7" data-reveal>
            <p class="display text-display-4 leading-snug text-ink first-letter:float-left first-letter:mr-3 first-letter:font-display first-letter:text-[5.2rem] first-letter:leading-[0.8] first-letter:text-brass">
                ARTFORM 3Di began with a simple belief: that technology, used with care, can give form to imagination in ways traditional methods cannot.
            </p>
            <p>What started with sculpture quickly grew. The same tools that let us print a finely detailed devotional figure let us prototype a product enclosure, or build the frame of a drone. Today the studio works across art, design and engineering — three disciplines connected by one way of making.</p>
            <p>Every project moves through the same hands: we listen, model, print and finish. That continuity is what allows us to deliver work that is technically precise and still feels personal.</p>
        </div>
    </div>
</section>

{{-- Mission & vision --}}
<section class="bg-ink text-bone">
    <div class="grid lg:grid-cols-2">
        <div class="border-b border-bone/10 px-5 py-24 sm:px-8 lg:border-r lg:border-b-0 lg:px-16 lg:py-36 xl:px-24">
            <x-eyebrow number="02" class="text-brass">Mission</x-eyebrow>
            <p class="display mt-10 text-display-3" data-split>To transform imagination into <em class="text-brass-soft">extraordinary, tangible creations</em> through thoughtful design and advanced manufacturing.</p>
        </div>
        <div class="px-5 py-24 sm:px-8 lg:px-16 lg:py-36 xl:px-24">
            <x-eyebrow number="03" class="text-brass">Vision</x-eyebrow>
            <p class="display mt-10 text-display-3" data-split>To be a respected name where <em class="text-brass-soft">art and engineering meet</em> — recognised for craft, innovation and integrity.</p>
        </div>
    </div>
</section>

{{-- Technology & innovation --}}
<section class="section overflow-x-clip bg-bone">
    <div class="container-x grid items-center gap-16 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <div class="media-frame aspect-[10/13]" data-reveal-image>
                <img src="{{ asset('assets/images/about/technology.svg') }}" alt="A digital wireframe model ready for printing" width="1000" height="1300" loading="lazy" decoding="async">
            </div>
        </div>
        <div class="lg:col-span-6 lg:col-start-7">
            <x-eyebrow number="04" class="text-smoke">Technology &amp; Innovation</x-eyebrow>
            <h2 class="display mt-8 text-display-2" data-split>Tools that <em>expand what’s possible.</em></h2>
            <p class="lead mt-8 text-ink/70" data-reveal>Digital sculpting, CAD and additive manufacturing let us realise geometry that would be impractical to carve or cast — and refine it quickly.</p>
            <ul class="mt-12 grid gap-px bg-ink/10 sm:grid-cols-3" data-reveal>
                @foreach ([['scan', 'Digital capture & modelling'], ['printer', 'Precision 3D printing'], ['wrench', 'Engineering & testing']] as [$icon, $label])
                    <li class="bg-bone p-6">
                        <x-icon :name="$icon" class="text-2xl text-brass" />
                        <p class="mt-4 text-[15px] font-medium">{{ $label }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- Creative manufacturing approach --}}
<section class="section bg-paper">
    <div class="container-x">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-6">
                <x-eyebrow number="05" class="text-smoke">Our Approach</x-eyebrow>
                <h2 class="display mt-8 text-display-2" data-split>Creative manufacturing, <em>done by hand and machine.</em></h2>
            </div>
            <div class="lg:col-span-5 lg:col-start-8">
                <div class="media-frame aspect-[10/13]" data-reveal-image>
                    <img src="{{ asset('assets/images/about/craft.svg') }}" alt="A hand-finished printed bust" width="1000" height="1300" loading="lazy" decoding="async">
                </div>
            </div>
        </div>

        <ol class="mt-20 grid gap-px bg-ink/10 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Art-led', 'Proportion, texture and emotion guide every decision — even on technical work.'],
                ['Engineered', 'Tolerances, materials and structure are considered from the very first model.'],
                ['Hand-finished', 'Printing is the beginning. Finishing by hand gives each piece its final character.'],
                ['Collaborative', 'We work closely with clients, sharing models and prototypes along the way.'],
            ] as $i => [$title, $text])
                <li class="bg-paper p-8 lg:p-10" data-reveal>
                    <span class="numeral text-6xl text-brass">0{{ $i + 1 }}</span>
                    <h3 class="display mt-8 text-3xl">{{ $title }}</h3>
                    <p class="mt-3 leading-relaxed text-ink/60">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- Team: rendered only when client details are available --}}
@if (! empty($team))
    <section class="section bg-bone">
        <div class="container-x">
            <x-eyebrow class="text-smoke">The Team</x-eyebrow>
            <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($team as $member)
                    <figure data-reveal>
                        <div class="media-frame aspect-[4/5]"><img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}" loading="lazy"></div>
                        <figcaption class="mt-5"><p class="display text-2xl">{{ $member['name'] }}</p><p class="mono mt-1 text-smoke">{{ $member['role'] }}</p></figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
@endif

<x-contact-cta tone="light" />

@endsection
