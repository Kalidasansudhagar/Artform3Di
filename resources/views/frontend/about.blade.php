@extends('layouts.app')

@section('title', 'About Us')
@section('description', 'Artform 3Di began in 2024 with one 3D printer and has grown into a 15-machine FDM printing and product-development setup in Sholinganallur, Chennai.')
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
            Built through consistency. <br class="hidden md:block"><em>Driven by</em> <span class="text-smoke">curiosity.</span>
        </h1>
        <div class="mt-14 grid gap-8 lg:grid-cols-12" data-reveal data-delay="0.7">
            <p class="lead text-ink/70 lg:col-span-5 lg:col-start-8">
                What began in 2024 with one 3D printer has grown into a 15-machine FDM printing and product-development setup in Sholinganallur, Chennai.
            </p>
        </div>
    </div>
    <div class="mt-20 lg:mt-28">
        <div class="media-frame aspect-[16/10] w-full sm:aspect-[16/8]" data-reveal-image>
            <img src="{{ asset('assets/images/about/studio.svg') }}" alt="A 3D printer at work in the Sholinganallur studio" width="1600" height="1100" loading="eager" decoding="async" class="scale-115" data-parallax="0.12">
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
                Artform 3Di began in 2024 with a simple idea: to turn a long-standing passion for engineering and making into something of my own.
            </p>
            <p>I am a mechanical engineer by qualification, but my career initially took me into the logistics industry. The interest in design, machines and manufacturing never faded. After my son was born, an opportunity came along to take a different path. I decided to give that passion a chance and started with one Bambu Lab P1S Combo 3D printer.</p>
            <p>In the beginning, it was just me, one machine and a lot to learn. Alongside my full-time job, I spent whatever time I could — one to three hours each day — learning the technology, experimenting with designs, taking on enquiries and building our presence online. The enquiries were modest at first, but they kept coming. Every project taught me something new, and every customer who trusted me gave me a reason to keep going.</p>
            <p>I chose to reinvest what the business earned back into its growth. Step by step, that first printer became a 15-machine FDM printing farm. We built a dedicated workspace in Sholinganallur, brought in dedicated CAD design support and expanded into projects across product development, rapid prototyping, parts restoration, automotive applications, electronics enclosures, drones, RC components, academic projects and more.</p>
            <p>Learning and sharing have been just as important as growing the business. I started the Tamil Nadu 3D Printing Community as a peer-to-peer space where anyone interested in 3D printing could ask questions, share knowledge and help others learn.</p>
            <p>This journey would not have been possible without the support of my wife and family, the trust of our customers and the encouragement of everyone who has been part of our community. From one printer to a growing 3D manufacturing setup, the journey has been built on consistency, curiosity and the willingness to keep experimenting.</p>
        </div>
    </div>
</section>

{{-- Mission & vision --}}
<section class="bg-ink text-bone">
    <div class="grid lg:grid-cols-2">
        <div class="border-b border-bone/10 px-5 py-24 sm:px-8 lg:border-r lg:border-b-0 lg:px-16 lg:py-36 xl:px-24">
            <x-eyebrow number="02" class="text-brass">Mission</x-eyebrow>
            <p class="display mt-10 text-display-3" data-split>To make product development and custom manufacturing <em class="text-brass-soft">more accessible</em> by combining design expertise, 3D printing technology and responsive project support.</p>
        </div>
        <div class="px-5 py-24 sm:px-8 lg:px-16 lg:py-36 xl:px-24">
            <x-eyebrow number="03" class="text-brass">Vision</x-eyebrow>
            <p class="display mt-10 text-display-3" data-split>To become a <em class="text-brass-soft">trusted product-development and additive-manufacturing partner</em> for businesses, creators, educational institutions and innovators across India.</p>
        </div>
    </div>
</section>

{{-- Technology & innovation --}}
<section class="section overflow-x-clip bg-bone">
    <div class="container-x grid items-center gap-16 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <div class="media-frame aspect-[10/13]" data-reveal-image>
                <img src="{{ asset('assets/images/about/technology.svg') }}" alt="A CAD model prepared for printing" width="1000" height="1300" loading="lazy" decoding="async">
            </div>
        </div>
        <div class="lg:col-span-6 lg:col-start-7">
            <x-eyebrow number="04" class="text-smoke">Our Setup</x-eyebrow>
            <h2 class="display mt-8 text-display-2" data-split>A 15-machine <em>FDM print farm.</em></h2>
            <p class="lead mt-8 text-ink/70" data-reveal>Machine build volumes from approximately 250 mm to 400 mm cube, a range of engineering and general-purpose materials, and dedicated CAD design support using SolidWorks and Fusion 360.</p>
            <ul class="mt-12 grid gap-px bg-ink/10 sm:grid-cols-3" data-reveal>
                @foreach ([['scan', 'CAD design & modelling'], ['printer', '15 FDM 3D printers'], ['wrench', 'Painting & finishing']] as [$icon, $label])
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
                <h2 class="display mt-8 text-display-2" data-split>Listen, understand, <em>explore what can be done.</em></h2>
            </div>
            <div class="lg:col-span-5 lg:col-start-8">
                <div class="media-frame aspect-[10/13]" data-reveal-image>
                    <img src="{{ asset('assets/images/about/craft.svg') }}" alt="A finished 3D printed piece" width="1000" height="1300" loading="lazy" decoding="async">
                </div>
            </div>
        </div>

        <ol class="mt-20 grid gap-px bg-ink/10 md:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Listen carefully', 'Every project starts with understanding what you need and how the part will be used.'],
                ['Understand the problem', 'We review geometry, material, tolerances and feasibility before quoting.'],
                ['Keep learning', 'An unfamiliar requirement is an opportunity to learn, not a reason to turn someone away.'],
                ['Stay committed', 'We explain the practical possibilities and stay committed to what we promise.'],
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
