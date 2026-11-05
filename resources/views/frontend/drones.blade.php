@extends('layouts.app')

@section('title', 'Drone Engineering')
@section('description', 'Custom drone manufacturing, rapid prototyping and research & development — drone engineering by ARTFORM 3Di.')
@section('header_theme', 'dark')
@section('body_class', 'bg-deep')

@section('content')

{{-- Cinematic hero with HUD framing --}}
<section class="blueprint relative isolate flex min-h-[100svh] items-end overflow-hidden bg-deep text-bone">
    <img src="{{ asset('assets/images/drones/hero.svg') }}" alt="" width="1920" height="1200" fetchpriority="high"
         class="absolute inset-0 -z-10 h-full w-full scale-110 object-cover opacity-80" data-parallax="0.14">
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-deep via-deep/40 to-deep/60"></div>

    {{-- HUD overlay --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-4 hidden sm:block lg:inset-8">
        <span class="absolute top-[var(--header-h)] left-0 h-8 w-8 border-t border-l border-steel/50"></span>
        <span class="absolute top-[var(--header-h)] right-0 h-8 w-8 border-t border-r border-steel/50"></span>
        <span class="absolute bottom-0 left-0 h-8 w-8 border-b border-l border-steel/50"></span>
        <span class="absolute right-0 bottom-0 h-8 w-8 border-r border-b border-steel/50"></span>
        <div class="mono absolute top-[calc(var(--header-h)+1.5rem)] right-6 space-y-1 text-right text-[10px] text-steel/70">
            <p>Division · Engineering</p>
            <p>Mode · Design / Build / Test</p>
        </div>
        <div class="absolute top-1/2 left-1/2 hidden h-40 w-40 -translate-x-1/2 -translate-y-1/2 lg:block">
            <span class="absolute inset-0 rounded-full border border-steel/20"></span>
            <span class="absolute top-1/2 left-0 h-px w-full bg-steel/25"></span>
            <span class="absolute top-0 left-1/2 h-full w-px bg-steel/25"></span>
        </div>
    </div>

    <div class="container-x relative pt-[calc(var(--header-h)+4rem)] pb-16 lg:pb-24">
        <p class="mono flex items-center gap-3 text-steel" data-reveal><span class="h-px w-8 bg-current"></span>Drone Engineering</p>
        <h1 class="display mt-8 max-w-5xl text-display-1" data-split data-split-immediate data-delay="0.3">Engineered <em class="text-steel">for Flight.</em></h1>
        <div class="mt-10 flex flex-col gap-10 lg:flex-row lg:items-end lg:justify-between">
            <p class="lead max-w-xl text-bone/70" data-reveal data-delay="0.8">Custom drone platforms designed, prototyped and built in-house — combining precision manufacturing with hands-on engineering.</p>
            <div class="flex flex-col gap-3 sm:flex-row" data-reveal data-delay="0.95">
                <x-button href="#capabilities" variant="light" size="lg" icon="arrow-down" data-no-transition>Capabilities</x-button>
                <x-button :href="route('contact').'#enquiry'" variant="outline-light" size="lg">Discuss a build</x-button>
            </div>
        </div>
    </div>
</section>

{{-- Introduction --}}
<section class="bg-deep py-24 text-bone md:py-32">
    <div class="container-x grid gap-14 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <p class="mono text-steel">[ 00 ] Overview</p>
        </div>
        <div class="lg:col-span-8">
            <p class="display text-display-4 leading-snug md:text-display-3" data-split>
                Our engineering division applies the precision of additive manufacturing to <em class="text-steel">unmanned aerial systems</em> — from first sketch to flight-ready platform.
            </p>
            <dl class="mt-14 grid gap-px bg-steel/15 sm:grid-cols-3" data-reveal>
                @foreach ([['Design', 'CAD-led airframe and component design'], ['Build', 'Printed and assembled in-house'], ['Test', 'Iterated through real-world trials']] as [$term, $desc])
                    <div class="bg-deep p-6">
                        <dt class="mono text-steel">{{ $term }}</dt>
                        <dd class="mt-3 text-sm leading-relaxed text-bone/60">{{ $desc }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</section>

{{-- Core services as spec-sheet panels --}}
@php
    $divisions = [
        ['code' => 'DE-01', 'title' => 'Custom Drone Manufacturing', 'image' => 'assets/images/drones/manufacturing.svg', 'text' => 'Bespoke airframes and assemblies designed around your payload, range and operating environment.', 'points' => ['Mission-specific frame design', 'Printed and composite structures', 'Assembly and integration']],
        ['code' => 'DE-02', 'title' => 'Rapid Prototyping', 'image' => 'assets/images/drones/prototyping.svg', 'text' => 'Print, fit, fly, refine. Fast design iterations shorten the path from concept to a proven platform.', 'points' => ['Quick-turn component printing', 'Fit and assembly validation', 'Design-test-iterate cycles']],
        ['code' => 'DE-03', 'title' => 'Research & Development', 'image' => 'assets/images/drones/research.svg', 'text' => 'Experimental platforms and test rigs for exploring new configurations, payloads and ideas.', 'points' => ['Concept exploration', 'Payload and mount experiments', 'Collaborative R&D projects']],
    ];
@endphp
<section id="capabilities" class="blueprint scroll-mt-20 border-t border-steel/15 bg-deep py-24 text-bone md:py-32">
    <div class="container-x">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <h2 class="display text-display-2" data-split>Three disciplines. <em class="text-steel">One workshop.</em></h2>
            <p class="mono text-steel" data-reveal>[ 01 — 03 ]</p>
        </div>

        <div class="mt-16 grid gap-px bg-steel/20 lg:grid-cols-3">
            @foreach ($divisions as $division)
                <article class="group flex flex-col bg-deep" data-reveal>
                    <div class="media-frame aspect-square bg-deep">
                        <img src="{{ asset($division['image']) }}" alt="{{ $division['title'] }}" width="1200" height="1200" loading="lazy" decoding="async"
                             class="transition-transform duration-[1400ms] ease-[var(--ease-expo)] group-hover:scale-105">
                        <span class="mono absolute top-4 left-4 bg-deep/80 px-2 py-1 text-[10px] text-steel ring-1 ring-steel/30">{{ $division['code'] }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-7 lg:p-9">
                        <h3 class="display text-3xl lg:text-4xl">{{ $division['title'] }}</h3>
                        <p class="mt-4 leading-relaxed text-bone/60">{{ $division['text'] }}</p>
                        <ul class="mt-8 space-y-3 border-t border-steel/15 pt-6 font-mono text-xs tracking-wide text-bone/70">
                            @foreach ($division['points'] as $point)
                                <li class="flex gap-3"><span class="text-steel">—</span>{{ $point }}</li>
                            @endforeach
                        </ul>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Engineering capabilities --}}
<section class="bg-[#11161a] py-24 text-bone md:py-32">
    <div class="container-x grid gap-14 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <p class="mono text-steel">[ 02 ] Capabilities</p>
            <h2 class="display mt-8 text-display-3" data-split>Engineering, <em class="text-steel">end to end.</em></h2>
            <p class="mt-6 max-w-sm leading-relaxed text-bone/55" data-reveal>Every stage of development handled by one team, so design intent carries all the way to the finished aircraft.</p>
        </div>
        <div class="grid gap-px bg-steel/15 sm:grid-cols-2 lg:col-span-8">
            @foreach ([
                ['pen-tool', 'Airframe Design', 'Structural CAD tuned for weight, stiffness and printability.'],
                ['cpu', 'Electronics Integration', 'Flight controller, power and wiring layouts planned and installed.'],
                ['wind', 'Propulsion Matching', 'Motor, propeller and battery combinations selected for the mission.'],
                ['package', 'Payload Integration', 'Custom mounts and housings for cameras and sensors.'],
                ['layers', 'Additive Manufacturing', 'Printed structural parts in engineering-grade materials.'],
                ['gauge', 'Testing & Iteration', 'Bench and field testing feeding directly into design revisions.'],
            ] as [$icon, $title, $text])
                <div class="group bg-[#11161a] p-7 transition-colors duration-500 hover:bg-deep lg:p-9" data-reveal>
                    <x-icon :name="$icon" class="text-2xl text-steel transition-transform duration-500 group-hover:-translate-y-1" />
                    <h3 class="mt-6 text-lg font-medium">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-bone/55">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Technical showcase with hotspots --}}
@php
    $hotspots = [
        ['label' => 'Airframe', 'x' => 58, 'y' => 36, 'text' => 'A rigid, lightweight central frame — printed, reinforced and optimised for each configuration.'],
        ['label' => 'Propulsion', 'x' => 67, 'y' => 21, 'text' => 'Motors and propellers matched to the target weight and flight profile.'],
        ['label' => 'Flight control', 'x' => 50, 'y' => 50, 'text' => 'Controller, sensors and power distribution laid out for clean, serviceable wiring.'],
        ['label' => 'Payload bay', 'x' => 50, 'y' => 63, 'text' => 'Custom mounts for cameras, sensors or experimental equipment.'],
    ];
@endphp
<section class="blueprint bg-deep py-24 text-bone md:py-32" x-data="{ active: 0 }">
    <div class="container-x grid items-center gap-14 lg:grid-cols-12">
        <div class="lg:col-span-5 lg:order-2">
            <p class="mono text-steel">[ 03 ] Technical Showcase</p>
            <h2 class="display mt-8 text-display-3" data-split>Inside <em class="text-steel">the build.</em></h2>
            <p class="mt-6 max-w-md leading-relaxed text-bone/55" data-reveal>Select a component to explore how each system comes together.</p>

            <ol class="mt-10 border-t border-steel/20" data-reveal>
                @foreach ($hotspots as $i => $spot)
                    <li class="border-b border-steel/20">
                        <button type="button" @click="active = {{ $i }}" :aria-expanded="active === {{ $i }}" class="flex w-full items-center gap-4 py-5 text-left">
                            <span class="font-mono text-xs" :class="active === {{ $i }} ? 'text-steel' : 'text-bone/35'">0{{ $i + 1 }}</span>
                            <span class="text-lg transition-colors" :class="active === {{ $i }} ? 'text-bone' : 'text-bone/50'">{{ $spot['label'] }}</span>
                            <x-icon name="plus" class="ml-auto text-steel transition-transform duration-500" ::class="active === {{ $i }} && 'rotate-45'" />
                        </button>
                        <div x-show="active === {{ $i }}" x-collapse @if ($i) x-cloak @endif>
                            <p class="pb-6 pl-9 text-sm leading-relaxed text-bone/60">{{ $spot['text'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="relative lg:col-span-7 lg:order-1">
            <div class="corner-frame relative aspect-square p-3">
                <div class="media-frame h-full w-full bg-deep">
                    <img src="{{ asset('assets/images/drones/blueprint.svg') }}" alt="Blueprint of a hexacopter platform" width="1600" height="1600" loading="lazy" decoding="async">
                </div>
                @foreach ($hotspots as $i => $spot)
                    <button type="button" @click="active = {{ $i }}" class="absolute flex h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center"
                            style="left: {{ $spot['x'] }}%; top: {{ $spot['y'] }}%" aria-label="{{ $spot['label'] }}">
                        <span class="absolute inset-2 rounded-full bg-steel/40" :class="active === {{ $i }} && 'animate-pulse-ring'"></span>
                        <span class="relative h-3 w-3 rounded-full ring-2 ring-deep transition-colors" :class="active === {{ $i }} ? 'bg-bone' : 'bg-steel'"></span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Project gallery --}}
@php
    $gallery = [
        ['title' => 'Survey Quad Airframe', 'tag' => 'Manufacturing', 'image' => 'assets/images/projects/survey-quad.svg'],
        ['title' => 'Hexacopter R&D Platform', 'tag' => 'Research', 'image' => 'assets/images/projects/hexacopter.svg'],
        ['title' => 'Printed Arm Iterations', 'tag' => 'Prototyping', 'image' => 'assets/images/drones/manufacturing.svg'],
        ['title' => 'Compact Test Frame', 'tag' => 'Prototyping', 'image' => 'assets/images/drones/prototyping.svg'],
    ];
@endphp
<section class="overflow-x-clip border-t border-steel/15 bg-deep py-24 text-bone md:py-32" data-slider="projects">
    <div class="container-x">
        <div class="flex flex-col gap-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mono text-steel">[ 04 ] Project Gallery</p>
                <h2 class="display mt-8 text-display-2" data-split>From the <em class="text-steel">workshop.</em></h2>
            </div>
            <div class="flex gap-2">
                <button type="button" data-prev class="icon-btn ring-1 ring-steel/30 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Previous"><x-icon name="arrow-left" /></button>
                <button type="button" data-next class="icon-btn ring-1 ring-steel/30 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Next"><x-icon name="arrow-right" /></button>
            </div>
        </div>
        <div class="swiper mt-14">
            <div class="swiper-wrapper">
                @foreach ($gallery as $i => $item)
                    <figure class="swiper-slide">
                        <div class="corner-frame p-2">
                            <div class="media-frame aspect-[7/5] bg-deep">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" width="1400" height="1000" loading="lazy" decoding="async">
                            </div>
                        </div>
                        <figcaption class="mt-5 flex items-baseline justify-between gap-4 border-t border-steel/15 pt-5">
                            <span class="text-lg">{{ $item['title'] }}</span>
                            <span class="mono shrink-0 text-steel">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} · {{ $item['tag'] }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
        <div class="slider-progress mt-12 text-steel"><span></span></div>
    </div>
</section>

<x-contact-cta tone="steel" eyebrow="Engineering enquiries" heading="Have a mission in mind? <em class='text-steel'>Let’s engineer it.</em>" text="Tell us about your application, payload and requirements — we’ll help shape the right platform." />

@endsection
