{{-- Horizontal timeline on desktop, vertical rail on mobile. The rail fills on scroll. --}}
@props(['tone' => 'light'])

@php
    $steps = [
        ['n' => '01', 'title' => 'Concept & Design', 'text' => 'We begin with your idea — sketches, references or a simple conversation — and define form, scale and finish.', 'image' => 'assets/images/process/concept.svg'],
        ['n' => '02', 'title' => 'Digital Modelling', 'text' => 'The concept is sculpted and engineered in 3D, refined until every curve and tolerance is right.', 'image' => 'assets/images/process/modelling.svg'],
        ['n' => '03', 'title' => 'Precision Printing', 'text' => 'Advanced printers build the piece layer by layer, with materials chosen for detail and durability.', 'image' => 'assets/images/process/printing.svg'],
        ['n' => '04', 'title' => 'Final Creation', 'text' => 'Each piece is finished by hand — cleaned, refined and coated — ready to be delivered.', 'image' => 'assets/images/process/final.svg'],
    ];
    $dark = $tone === 'dark';
@endphp

<div data-rail-scope {{ $attributes->class('relative') }}>
    {{-- Rail --}}
    <div data-rail aria-hidden="true" @class([
        'absolute top-2 bottom-2 left-[7px] w-px lg:top-[7px] lg:right-0 lg:bottom-auto lg:left-0 lg:h-px lg:w-full',
        'bg-ink/15' => ! $dark, 'bg-bone/15' => $dark,
    ])>
        <span data-rail-fill class="absolute inset-0 origin-top bg-brass lg:origin-left"></span>
    </div>

    <ol class="relative grid gap-14 lg:grid-cols-4 lg:gap-10">
        @foreach ($steps as $step)
            <li class="relative pl-12 lg:pt-14 lg:pl-0" data-reveal>
                <span aria-hidden="true" @class(['absolute top-1 left-0 h-[15px] w-[15px] rounded-full border-2 border-brass lg:top-0', 'bg-paper' => ! $dark, 'bg-ink' => $dark])></span>
                <p class="numeral text-7xl text-brass lg:text-8xl">{{ $step['n'] }}</p>
                <h3 class="display mt-5 text-3xl">{{ $step['title'] }}</h3>
                <p @class(['mt-3 max-w-xs leading-relaxed', 'text-ink/65' => ! $dark, 'text-bone/60' => $dark])>{{ $step['text'] }}</p>
                <div class="media-frame mt-7 aspect-[9/7] max-w-sm" data-reveal-image>
                    <img src="{{ asset($step['image']) }}" alt="{{ $step['title'] }}" width="900" height="700" loading="lazy" decoding="async">
                </div>
            </li>
        @endforeach
    </ol>
</div>
