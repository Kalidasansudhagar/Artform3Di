@extends('layouts.app')

@section('title', '3D Printing Services')
@section('description', 'Rapid prototyping, custom 3D modelling and premium art & decor — precision 3D printing services by ARTFORM 3Di.')
@section('header_theme', 'dark')

@section('content')

{{-- Hero --}}
<section class="relative isolate flex min-h-[88svh] items-end overflow-hidden bg-ink text-bone">
    <img src="{{ asset('assets/images/services/hero.svg') }}" alt="" width="1920" height="1100" fetchpriority="high"
         class="absolute inset-0 -z-10 h-full w-full scale-110 object-cover" data-parallax="0.1">
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-ink via-ink/40 to-ink/30"></div>

    <div class="container-x pt-[calc(var(--header-h)+4rem)] pb-16 lg:pb-24">
        <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-8">
                <x-eyebrow class="text-brass-soft" data-reveal>3D Printing Services</x-eyebrow>
                <h1 class="display mt-8 text-display-1" data-split data-split-immediate data-delay="0.3">Precision in <em class="text-brass-soft">every layer.</em></h1>
            </div>
            <div class="lg:col-span-4" data-reveal data-delay="0.8">
                <p class="leading-relaxed text-bone/70">Design, modelling, printing and finishing under one roof — for prototypes, bespoke objects and statement art.</p>
                <x-button href="#rapid-prototyping" variant="light" class="mt-8" icon="arrow-down" data-no-transition>View services</x-button>
            </div>
        </div>
    </div>
</section>

{{-- Introduction --}}
<section class="section bg-paper">
    <div class="container-x grid gap-12 lg:grid-cols-12">
        <x-eyebrow number="00" class="text-smoke lg:col-span-3">Introduction</x-eyebrow>
        <div class="lg:col-span-9">
            <p class="display text-display-4 leading-snug md:text-display-3" data-split>
                We combine the discipline of engineering with the sensitivity of fine art — so a functional bracket and a devotional sculpture receive the <em>same uncompromising care.</em>
            </p>
            <div class="mt-14 grid gap-8 border-t border-ink/10 pt-10 text-ink/65 sm:grid-cols-3" data-reveal>
                @foreach ([['layers', 'Fine-layer printing', 'Detail-focused settings for crisp surfaces and accurate geometry.'], ['pen-tool', 'In-house modelling', 'Digital sculpting and CAD, prepared specifically for printing.'], ['sparkles', 'Hand finishing', 'Sanding, priming, painting and patina for a refined result.']] as [$icon, $title, $text])
                    <div>
                        <x-icon :name="$icon" class="text-2xl text-brass" />
                        <h3 class="mt-4 font-semibold text-ink">{{ $title }}</h3>
                        <p class="mt-2 text-[15px] leading-relaxed">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- The three services, as large alternating editorial splits --}}
@php
    $services = [
        [
            'id' => 'rapid-prototyping', 'title' => 'Rapid Prototyping', 'image' => 'assets/images/services/rapid-prototyping.svg',
            'lead' => 'Turn concepts into physical parts quickly — so you can hold, test and improve them before investing in production.',
            'points' => ['Form, fit and function prototypes', 'Iterative design revisions', 'Functional mechanical parts', 'Enclosures, jigs and fixtures'],
        ],
        [
            'id' => 'custom-modelling', 'title' => 'Custom 3D Modelling', 'image' => 'assets/images/services/custom-modelling.svg',
            'lead' => 'From a sketch, a photograph or simply an idea, we build accurate, print-ready digital models — sculpted or engineered.',
            'points' => ['Digital sculpting for art and figurines', 'CAD modelling for technical parts', 'Model repair and print preparation', 'Scaling and detailing for any size'],
        ],
        [
            'id' => 'art-decor', 'title' => 'Premium Art & Decor', 'image' => 'assets/images/services/art-decor.svg',
            'lead' => 'Sculptures, vessels and statement pieces for homes, temples, hospitality and corporate interiors — designed and finished to impress.',
            'points' => ['Devotional and spiritual sculpture', 'Decorative objects and vessels', 'Commissioned installations', 'Corporate gifts and awards'],
        ],
    ];
@endphp

<section class="bg-paper pb-12 lg:pb-24">
    <div class="container-x space-y-28 lg:space-y-40">
        @foreach ($services as $i => $service)
            <article id="{{ $service['id'] }}" class="grid scroll-mt-28 gap-10 lg:grid-cols-12 lg:items-center lg:gap-16">
                <div @class(['relative lg:col-span-7', 'lg:order-2' => $i % 2])>
                    <div class="media-frame aspect-[7/5]" data-reveal-image>
                        <img src="{{ asset($service['image']) }}" alt="{{ $service['title'] }}" width="1400" height="1000" loading="lazy" decoding="async">
                    </div>
                    <span aria-hidden="true" @class(['numeral absolute -top-10 text-[7rem] text-brass lg:-top-16 lg:text-[11rem]', '-left-2 lg:-left-8' => ! ($i % 2), '-right-2 lg:-right-8' => $i % 2])>0{{ $i + 1 }}</span>
                </div>
                <div @class(['lg:col-span-5', 'lg:order-1' => $i % 2])>
                    <p class="mono text-smoke" data-reveal>Service 0{{ $i + 1 }}</p>
                    <h2 class="display mt-5 text-display-3" data-split>{{ $service['title'] }}</h2>
                    <p class="lead mt-6 text-ink/70" data-reveal>{{ $service['lead'] }}</p>
                    <ul class="mt-10 border-t border-ink/10" data-reveal>
                        @foreach ($service['points'] as $point)
                            <li class="flex items-center gap-4 border-b border-ink/10 py-4 text-[15px]"><x-icon name="check" class="text-brass" />{{ $point }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact') }}#enquiry" class="link-line is-static mt-10 text-sm font-semibold tracking-[0.12em] uppercase" data-reveal>Discuss your project <x-icon name="arrow-right" /></a>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- Materials --}}
@php
    // Indicative material range — confirm the final list with the client.
    $materials = [
        ['PLA / PLA+', 'Fine detail, smooth surface, wide colour range', 'Sculpture, decor, concept models', '#d8cbb4'],
        ['Silk & Metallic PLA', 'Lustrous finish with a metallic sheen', 'Decorative and devotional pieces', '#b59a6d'],
        ['PETG', 'Tough, slightly flexible, moisture resistant', 'Functional parts and enclosures', '#8fa0ae'],
        ['ABS / ASA', 'Heat and impact resistant, post-processable', 'Engineering prototypes, outdoor parts', '#5f5a52'],
        ['TPU', 'Flexible and elastic, absorbs impact', 'Gaskets, grips, protective parts', '#2a2a2e'],
        ['Carbon-fibre composites', 'Stiff and lightweight with a matte finish', 'Drone frames, brackets, jigs', '#1a1a1c'],
        ['High-detail resin', 'Exceptionally fine detail and crisp edges', 'Miniatures, figurines, masters', '#ece7df'],
    ];
@endphp
<section class="section bg-ink text-bone">
    <div class="container-x">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <x-eyebrow class="text-bone/50">Printing Materials</x-eyebrow>
                <h2 class="display mt-8 text-display-2" data-split>The right material <em class="text-brass-soft">for every idea.</em></h2>
            </div>
            <p class="max-w-md self-end leading-relaxed text-bone/55 lg:col-span-4 lg:col-start-9" data-reveal>We recommend materials based on the look, strength and environment your piece needs. Not sure? We’ll advise.</p>
        </div>

        <div class="mt-16 border-t border-bone/15" role="table" aria-label="Printing materials">
            <div role="row" class="mono hidden grid-cols-12 gap-6 py-4 text-bone/40 md:grid">
                <span role="columnheader" class="col-span-4">Material</span>
                <span role="columnheader" class="col-span-4">Character</span>
                <span role="columnheader" class="col-span-4">Best for</span>
            </div>
            @foreach ($materials as [$name, $character, $use, $swatch])
                <div role="row" class="group grid gap-2 border-t border-bone/10 py-6 transition-colors duration-500 hover:bg-bone/[0.03] md:grid-cols-12 md:gap-6 md:py-7" data-reveal>
                    <span role="cell" class="flex items-center gap-4 md:col-span-4">
                        <span class="h-3 w-3 rounded-full ring-1 ring-bone/20 transition-transform duration-500 group-hover:scale-[2]" style="background: {{ $swatch }}"></span>
                        <span class="display text-2xl md:text-3xl">{{ $name }}</span>
                    </span>
                    <span role="cell" class="pl-7 text-bone/60 md:col-span-4 md:pl-0">{{ $character }}</span>
                    <span role="cell" class="pl-7 text-bone/60 md:col-span-4 md:pl-0">{{ $use }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Manufacturing process --}}
<section class="section bg-bone">
    <div class="container-x">
        <div class="mb-20 max-w-2xl lg:mb-24">
            <x-eyebrow class="text-smoke">Manufacturing Process</x-eyebrow>
            <h2 class="display mt-8 text-display-2" data-split>Four steps, <em>one standard.</em></h2>
        </div>
        <x-process-timeline />
    </div>
</section>

{{-- Project showcase --}}
<section class="section overflow-x-clip bg-paper" data-slider="projects">
    <div class="container-x">
        <div class="flex flex-col gap-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <x-eyebrow class="text-smoke">Project Showcase</x-eyebrow>
                <h2 class="display mt-8 text-display-2" data-split>Made in <em>the studio.</em></h2>
            </div>
            <div class="flex gap-2">
                <button type="button" data-prev class="icon-btn ring-1 ring-ink/15 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Previous"><x-icon name="arrow-left" /></button>
                <button type="button" data-next class="icon-btn ring-1 ring-ink/15 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Next"><x-icon name="arrow-right" /></button>
            </div>
        </div>
        <div class="swiper mt-14" data-reveal>
            <div class="swiper-wrapper">
                @foreach ($projects as $project)
                    <figure class="swiper-slide group">
                        <div class="media-frame aspect-[7/5]">
                            <img src="{{ asset($project['image']) }}" alt="{{ $project['title'] }}" width="1400" height="1000" loading="lazy" decoding="async" class="transition-transform duration-[1400ms] ease-[var(--ease-expo)] group-hover:scale-105">
                        </div>
                        <figcaption class="mt-6 flex items-baseline justify-between gap-6">
                            <span class="display text-3xl">{{ $project['title'] }}</span>
                            <span class="mono shrink-0 text-smoke">{{ $project['category'] }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
        <div class="slider-progress mt-12 text-ink"><span></span></div>
    </div>
</section>

<x-contact-cta tone="dark" eyebrow="Your project" heading="Ready to print <em>something remarkable?</em>" text="Share your idea, drawing or file — we’ll recommend the best approach, material and finish." />

@endsection
