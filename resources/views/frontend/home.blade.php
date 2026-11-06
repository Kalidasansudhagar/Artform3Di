@extends('layouts.app')

@section('header_theme', 'dark')

@php
    $video = config('artform.hero_video');
    $hasVideo = file_exists(public_path($video['desktop']));
    $hasMobileVideo = file_exists(public_path($video['mobile']));
@endphp

@push('head')
    <link rel="preload" as="image" href="{{ asset($video['poster']) }}">
@endpush

@section('content')

{{-- ============================================================= --}}
{{-- 1 · Cinematic hero                                             --}}
{{-- ============================================================= --}}
<section class="relative isolate flex min-h-[100svh] items-end overflow-hidden bg-ink text-bone">
    <div class="absolute inset-0 -z-10">
        <img src="{{ asset($video['poster']) }}" alt="" width="1920" height="1080" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">

        @if ($hasVideo)
            {{-- Footage brief: nozzle close-up → extrusion → layers forming → orbit → finished sculpture reveal. --}}
            <video data-hero-video class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-1000"
                   autoplay muted loop playsinline preload="none" poster="{{ asset($video['poster']) }}"
                   data-src-desktop="{{ asset($video['desktop']) }}"
                   @if ($hasMobileVideo) data-src-mobile="{{ asset($video['mobile']) }}" @endif
                   aria-hidden="true"></video>
        @else
            <canvas data-hero-print class="absolute inset-0 h-full w-full opacity-0 transition-opacity duration-1000" aria-hidden="true"></canvas>
        @endif

        {{-- Overlays keep copy legible over any footage --}}
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/35 to-ink/50"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-ink/80 via-ink/20 to-transparent"></div>
    </div>

    <div class="container-x pt-[calc(var(--header-h)+3rem)] pb-14 sm:pb-20 lg:pb-24">
        <div class="max-w-4xl">
            <p class="mono flex items-center gap-3 text-brass-soft" data-reveal data-delay="0.2">
                <span>3D Printing</span><span class="h-1 w-1 rounded-full bg-current"></span><span>CAD Design</span><span class="h-1 w-1 rounded-full bg-current"></span><span>Prototyping</span>
            </p>
            <h1 class="display mt-6 text-display-1" data-split data-split-immediate data-delay="0.35">
                Where Design <br class="hidden sm:block">Meets <em class="text-brass-soft">Technology.</em>
            </h1>
            <p class="lead mt-8 max-w-xl text-bone/75" data-reveal data-delay="0.9">
                FDM 3D printing, CAD design, rapid prototyping and custom part reproduction in Chennai. From one-off prototypes to small-batch production, Artform 3Di helps bring your ideas to life.
            </p>
            <div class="mt-10 flex flex-col gap-3 sm:flex-row" data-reveal data-delay="1.05">
                <x-button :href="route('contact').'#enquiry'" variant="light" size="lg">Request a Quote</x-button>
                <x-button :href="route('services')" variant="outline-light" size="lg" icon="arrow-right">Explore Our Services</x-button>
            </div>
        </div>

        <div class="mt-14 flex items-end justify-between border-t border-bone/15 pt-6" data-reveal data-delay="1.2">
            @unless ($hasVideo)
                <p class="mono flex items-center gap-3 text-bone/55">
                    <span class="relative flex h-2 w-2"><span class="absolute inset-0 animate-pulse-ring rounded-full bg-brass"></span><span class="relative h-2 w-2 rounded-full bg-brass"></span></span>
                    <span data-print-status>Printing</span> · <span data-layer-counter class="tabular-nums">Layer 001 / 150</span>
                </p>
            @else
                <p class="mono text-bone/55">15-machine FDM print farm · Chennai</p>
            @endunless
            <a href="#studio" class="hidden items-center gap-4 text-bone/60 transition hover:text-bone sm:flex" data-no-transition>
                <span class="mono">Scroll</span>
                <span class="relative block h-12 w-px overflow-hidden bg-bone/20"><span class="animate-scroll-cue absolute inset-0 bg-bone"></span></span>
            </a>
        </div>
    </div>
</section>

{{-- ============================================================= --}}
{{-- 2 · Brand introduction                                         --}}
{{-- ============================================================= --}}
<section id="studio" class="section overflow-x-clip bg-paper">
    <div class="container-x grid items-center gap-16 lg:grid-cols-12 lg:gap-10">
        <div class="relative lg:col-span-5">
            <div class="media-frame aspect-[10/13]" data-reveal-image>
                <img src="{{ asset('assets/images/home/intro-studio.svg') }}" alt="A 3D printer in the Artform 3Di print farm" width="1000" height="1300" loading="lazy" decoding="async" data-parallax="0.12" class="scale-115">
            </div>
            <div class="media-frame absolute -right-4 -bottom-12 aspect-square w-[46%] ring-8 ring-paper sm:-right-10 lg:-right-20" data-reveal-image>
                <img src="{{ asset('assets/images/home/intro-detail.svg') }}" alt="Close-up of FDM print layers" width="900" height="900" loading="lazy" decoding="async">
            </div>
        </div>

        <div class="lg:col-span-6 lg:col-start-7">
            <x-eyebrow number="01" class="text-smoke">The Studio</x-eyebrow>
            <h2 class="display mt-8 text-display-2" data-split>From concept <em>to component.</em></h2>
            <div class="mt-10 grid gap-6 text-ink/70 sm:grid-cols-2" data-reveal>
                <p class="leading-relaxed">At Artform 3Di, we turn ideas, designs and real-world problems into physical solutions. Based in Sholinganallur, Chennai, we provide FDM 3D printing, rapid prototyping, CAD modelling, parts restoration and small-batch manufacturing.</p>
                <p class="leading-relaxed">Our 15-machine print farm, engineering and general-purpose materials and dedicated CAD support in SolidWorks and Fusion 360 help customers move from concept to a usable physical part.</p>
            </div>
            <ul class="mt-12 grid grid-cols-3 border-t border-ink/10" data-reveal>
                @foreach (['Design', 'Prototyping', 'Production'] as $i => $discipline)
                    <li class="border-r border-ink/10 pt-5 pr-4 last:border-r-0 [&:not(:first-child)]:pl-4">
                        <span class="font-mono text-[11px] text-brass">0{{ $i + 1 }}</span>
                        <span class="display mt-2 block text-xl sm:text-2xl">{{ $discipline }}</span>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('about') }}" class="link-line is-static mt-12 text-sm font-semibold tracking-[0.12em] uppercase" data-reveal>Our story <x-icon name="arrow-right" /></a>
        </div>
    </div>
</section>

{{-- ============================================================= --}}
{{-- 3 · Featured masterpieces                                      --}}
{{-- ============================================================= --}}
<section class="section overflow-x-clip bg-ink text-bone" data-slider="products">
    <div class="container-x">
        <div class="flex flex-col gap-10 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <x-eyebrow number="02" class="text-bone/50">The Shop</x-eyebrow>
                <h2 class="display mt-8 text-display-2" data-split>Featured <em class="text-brass-soft">Creations</em></h2>
            </div>
            <div class="flex max-w-md flex-col gap-6 lg:items-end lg:text-right" data-reveal>
                <p class="leading-relaxed text-bone/60">Statues, miniatures, personalised gifts and custom models — designed and 3D printed in our Chennai studio.</p>
                <a href="{{ route('shop.index') }}" class="link-line is-static text-sm font-semibold tracking-[0.12em] uppercase">Visit the Shop <x-icon name="arrow-right" /></a>
            </div>
        </div>

        <ul class="mt-12 flex gap-2 overflow-x-auto pb-2 [scrollbar-width:none]" data-reveal>
            @foreach (\App\Support\Catalog::CATEGORIES as $slug => $name)
                <li class="shrink-0"><a href="{{ route('shop.index', ['category' => $slug]) }}" class="block rounded-full px-4 py-2 text-xs tracking-wide text-bone/70 ring-1 ring-bone/15 transition hover:bg-bone hover:text-ink">{{ $name }}</a></li>
            @endforeach
        </ul>

        <div class="swiper mt-12" data-reveal>
            <div class="swiper-wrapper">
                @foreach ($featured as $product)
                    <div class="swiper-slide">
                        <x-product-card :product="$product" tone="dark" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-14 flex items-center gap-8">
            <div class="slider-progress flex-1 text-bone"><span></span></div>
            <div class="flex gap-2">
                <button type="button" data-prev class="icon-btn ring-1 ring-bone/20 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Previous"><x-icon name="arrow-left" /></button>
                <button type="button" data-next class="icon-btn ring-1 ring-bone/20 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Next"><x-icon name="arrow-right" /></button>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================= --}}
{{-- 4 · 3D printing services                                       --}}
{{-- ============================================================= --}}
@php
    $services = [
        ['id' => 'rapid-prototyping', 'title' => 'Rapid Prototyping', 'text' => 'Physical prototypes for fit checks, design iteration and proof-of-concept work — plus small-batch production on our 15-machine farm.', 'image' => 'assets/images/services/rapid-prototyping.svg'],
        ['id' => 'custom-modelling', 'title' => 'CAD & Parts Restoration', 'text' => 'CAD design in SolidWorks and Fusion 360, and recreation of broken, unavailable or obsolete parts from a sample, measurements or photos.', 'image' => 'assets/images/services/custom-modelling.svg'],
        ['id' => 'art-decor', 'title' => 'Models, Gifts & Finishing', 'text' => 'Statues, miniatures, personalised gifts and display models, with painting available for selected parts.', 'image' => 'assets/images/services/art-decor.svg'],
    ];
@endphp
<section class="section bg-paper">
    <div class="container-x">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <x-eyebrow number="03" class="text-smoke">3D Printing Services</x-eyebrow>
                <h2 class="display mt-8 text-display-2" data-split>From Idea <em>to Physical Part.</em></h2>
            </div>
            <p class="max-w-md self-end leading-relaxed text-ink/65 lg:col-span-4 lg:col-start-9" data-reveal>
                One studio for design, modelling, printing and finishing — whether you need a single prototype, a recreated part or a small production batch.
            </p>
        </div>

        {{-- Expanding panels on desktop, stacked full-width visuals on mobile --}}
        <div x-data="{ active: 0 }" class="mt-16 flex flex-col gap-3 lg:h-[680px] lg:flex-row" data-reveal>
            @foreach ($services as $i => $service)
                <a href="{{ route('services') }}#{{ $service['id'] }}"
                   @mouseenter="active = {{ $i }}" @focus="active = {{ $i }}"
                   :class="active === {{ $i }} ? 'lg:flex-[2.4]' : 'lg:flex-1'"
                   class="group relative isolate block min-h-[440px] overflow-hidden bg-char text-bone transition-[flex] duration-[900ms] ease-[var(--ease-expo)] sm:min-h-[520px] lg:min-h-0">
                    <img src="{{ asset($service['image']) }}" alt="" width="1400" height="1000" loading="lazy" decoding="async"
                         class="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-[1400ms] ease-[var(--ease-expo)] group-hover:scale-105">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-ink/90 via-ink/30 to-transparent"></div>

                    <div class="flex h-full flex-col justify-between p-7 lg:p-10">
                        <div class="flex items-start justify-between">
                            <span class="font-mono text-xs text-brass-soft">0{{ $i + 1 }} / 03</span>
                            <span class="flex h-12 w-12 items-center justify-center rounded-full ring-1 ring-bone/30 transition duration-500 group-hover:bg-bone group-hover:text-ink">
                                <x-icon name="arrow-up-right" />
                            </span>
                        </div>
                        <div>
                            <h3 class="display max-w-[11ch] text-4xl lg:text-5xl">{{ $service['title'] }}</h3>
                            <p class="mt-4 max-w-md leading-relaxed text-bone/70 transition-opacity duration-700 lg:opacity-0"
                               :class="active === {{ $i }} && 'lg:opacity-100 lg:delay-200'">{{ $service['text'] }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-14 flex justify-center" data-reveal>
            <x-button :href="route('services')" variant="dark" size="lg">Explore Our Services</x-button>
        </div>
    </div>
</section>

{{-- ============================================================= --}}
{{-- 5 · Creative manufacturing process                             --}}
{{-- ============================================================= --}}
<section class="section bg-bone">
    <div class="container-x">
        <div class="mb-20 flex flex-col gap-8 lg:mb-24 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <x-eyebrow number="04" class="text-smoke">The Process</x-eyebrow>
                <h2 class="display mt-8 text-display-2" data-split>How It <em>Works.</em></h2>
            </div>
            <p class="max-w-sm leading-relaxed text-ink/65" data-reveal>A clear path from your first message to a finished, checked part.</p>
        </div>
        <x-process-timeline />
    </div>
</section>

{{-- ============================================================= --}}
{{-- 6 · Drone engineering                                          --}}
{{-- ============================================================= --}}
<section class="blueprint relative overflow-hidden bg-deep py-24 text-bone md:py-32 lg:py-40">
    <div class="container-x grid items-center gap-16 lg:grid-cols-12 lg:gap-12">
        <div class="lg:col-span-5">
            <p class="mono flex items-center gap-3 text-steel"><span class="h-px w-8 bg-current"></span>05 · Drone &amp; RC Components</p>
            <h2 class="display mt-8 text-display-2" data-split>Printed Parts <em class="text-steel">for Drones</em> &amp; RC.</h2>
            <p class="mt-8 max-w-md leading-relaxed text-bone/60" data-reveal>
                Our projects include drone and RC components — CAD-designed and 3D printed frames, mounts and custom parts in engineering materials such as PETG, Nylon and Nylon-CF.
            </p>

            <ul class="mt-12 border-t border-steel/20" data-reveal>
                @foreach ([
                    ['Frames & Mounts', 'Printed frames, arms, brackets and mounts for drone and RC builds.'],
                    ['Rapid Prototyping', 'Quick design iterations for fit checks and refinement.'],
                    ['Custom Components', 'Enclosures, covers and project-specific parts designed in CAD.'],
                ] as $i => [$title, $text])
                    <li class="grid grid-cols-[3rem_1fr] gap-4 border-b border-steel/20 py-6">
                        <span class="font-mono text-xs text-steel">[0{{ $i + 1 }}]</span>
                        <div>
                            <h3 class="text-lg font-medium">{{ $title }}</h3>
                            <p class="mt-1 text-sm text-bone/50">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-12" data-reveal>
                <x-button :href="route('drones')" variant="light" size="lg">Explore Drone &amp; RC Parts</x-button>
            </div>
        </div>

        <div class="relative lg:col-span-7">
            <div class="corner-frame relative aspect-square p-3">
                <div class="media-frame h-full w-full bg-deep" data-reveal-image>
                    <img src="{{ asset('assets/images/drones/home-feature.svg') }}" alt="Technical top view of a quadcopter airframe" width="1400" height="1400" loading="lazy" decoding="async">
                </div>
                {{-- Scanning ring --}}
                <div aria-hidden="true" class="pointer-events-none absolute inset-[12%] rounded-full border border-dashed border-steel/25 motion-safe:animate-spin-slow"></div>
                {{-- Callouts --}}
                @foreach ([['Frame', 'top-[8%] left-[6%]'], ['Motor mounts', 'top-[14%] right-[6%]'], ['Electronics housing', 'bottom-[16%] left-[6%]'], ['Payload mount', 'bottom-[8%] right-[8%]']] as [$label, $pos])
                    <span class="mono absolute {{ $pos }} flex items-center gap-2 bg-deep/80 px-2 py-1 text-[10px] text-steel ring-1 ring-steel/30 backdrop-blur" data-reveal>
                        <span class="h-1.5 w-1.5 rounded-full bg-steel"></span>{{ $label }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============================================================= --}}
{{-- 7 · Featured projects                                          --}}
{{-- ============================================================= --}}
<section class="section bg-paper">
    <div class="container-x">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <x-eyebrow number="06" class="text-smoke">Projects</x-eyebrow>
                <h2 class="display mt-8 text-display-2" data-split>What We <em>Work On</em></h2>
            </div>
            <p class="max-w-sm leading-relaxed text-ink/65" data-reveal>From automotive parts and enclosures to statues and personalised gifts — real problems turned into practical parts.</p>
        </div>

        {{-- Editorial grid: varied spans and offsets rather than uniform cards --}}
        @php
            $layout = [
                'lg:col-span-7',
                'lg:col-span-5 lg:mt-40',
                'lg:col-span-4',
                'lg:col-span-4 lg:mt-24',
                'lg:col-span-4',
                'lg:col-span-6 lg:col-start-4 lg:mt-12',
            ];
            $ratios = ['aspect-[7/5]', 'aspect-[4/5]', 'aspect-[4/5]', 'aspect-square', 'aspect-[4/5]', 'aspect-[16/10]'];
        @endphp
        <div class="mt-20 grid gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-12 lg:gap-y-24">
            @foreach ($projects as $i => $project)
                <article class="group {{ $layout[$i] }}">
                    <div class="media-frame {{ $ratios[$i] }}" data-reveal-image>
                        <img src="{{ asset($project['image']) }}" alt="{{ $project['title'] }}" width="1400" height="1000" loading="lazy" decoding="async"
                             class="transition-transform duration-[1400ms] ease-[var(--ease-expo)] group-hover:scale-105">
                    </div>
                    <div class="mt-6 flex items-start justify-between gap-6" data-reveal>
                        <div>
                            <p class="mono text-smoke">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} — {{ $project['category'] }}</p>
                            <h3 class="display mt-3 text-3xl">{{ $project['title'] }}</h3>
                            <p class="mt-3 max-w-md text-[15px] leading-relaxed text-ink/60">{{ $project['text'] }}</p>
                        </div>
                        <x-icon name="arrow-up-right" class="mt-1 shrink-0 text-xl text-smoke transition duration-500 group-hover:rotate-45 group-hover:text-ink" />
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================= --}}
{{-- 8 · Why ARTFORM 3Di                                            --}}
{{-- ============================================================= --}}
<section class="section bg-ink text-bone">
    <div class="container-x grid gap-16 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-32">
                <x-eyebrow number="07" class="text-bone/50">Why ARTFORM 3Di</x-eyebrow>
                <h2 class="display mt-8 text-display-3" data-split>What we bring to <em class="text-brass-soft">every project.</em></h2>
                <p class="mt-8 max-w-sm leading-relaxed text-bone/55" data-reveal>We listen carefully, understand the problem and explore what can be done — then stay committed to what we promise.</p>
            </div>
        </div>

        <ol class="lg:col-span-8" data-spotlight>
            @foreach ([
                ['Rapid Prototyping', 'Convert designs and concepts into physical parts for fit, form and functional evaluation.'],
                ['Parts Restoration', 'Recreate broken, unavailable or obsolete plastic components from a usable sample, dimensions or reference design.'],
                ['In-house CAD Design', 'Design and modelling support in SolidWorks and Fusion 360 — not only printing from supplied files.'],
                ['15-Machine Print Farm', 'Parallel jobs and small-batch production, with machine build volumes from about 250 mm to 400 mm cube.'],
                ['Material Flexibility', 'PLA, PETG, ABS, ASA, TPU, Nylon and Nylon-CF, chosen for the part’s functional requirements.'],
            ] as $i => [$title, $text])
                <li class="group grid gap-4 border-t border-bone/10 py-10 transition-opacity duration-700 last:border-b sm:grid-cols-[5rem_1fr] lg:py-12">
                    <span class="font-mono text-xs text-brass">0{{ $i + 1 }}</span>
                    <div class="grid gap-4 lg:grid-cols-[1.3fr_1fr] lg:items-baseline lg:gap-10">
                        <h3 class="display text-4xl transition-transform duration-700 ease-[var(--ease-expo)] group-hover:translate-x-2 sm:text-5xl">{{ $title }}</h3>
                        <p class="leading-relaxed text-bone/55">{{ $text }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============================================================= --}}
{{-- 9 · Contact CTA                                                --}}
{{-- ============================================================= --}}
<x-contact-cta tone="light" />

@endsection
