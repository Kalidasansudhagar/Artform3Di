@extends('layouts.app')

@section('title', 'Contact & Get a Quote')
@section('description', 'Request a 3D printing, CAD design or prototyping quote from Artform 3Di, Sholinganallur, Chennai.'.(config('artform.contact.phone') ? ' Call or WhatsApp '.config('artform.contact.phone').'.' : ''))
@section('header_theme', 'dark')
@section('header_solid', true)

@php
    // Phone, WhatsApp and address come from Admin → Settings.
    $phone = \App\Support\Contact::phone();
    $whatsapp = \App\Support\Contact::whatsappUrl();
    $address = \App\Support\Contact::address();
@endphp

@section('content')

<section class="grid min-h-[100svh] bg-paper lg:grid-cols-12">
    {{-- Information panel --}}
    <div class="relative flex flex-col justify-between overflow-hidden bg-ink px-5 pt-[calc(var(--header-h)+4rem)] pb-14 text-bone sm:px-8 lg:col-span-5 lg:px-12 lg:pb-16 xl:px-16">
        <svg aria-hidden="true" viewBox="0 0 600 520" class="pointer-events-none absolute -right-40 -bottom-20 w-[640px] opacity-[0.035]" fill="currentColor">
            @for ($i = 0; $i < 26; $i++)
                <rect x="{{ 300 - 11 * ($i + 1) }}" y="{{ 10 + $i * 20 }}" width="{{ 22 * ($i + 1) }}" height="7" rx="3.5" />
            @endfor
        </svg>

        <div class="relative">
            <x-eyebrow class="text-brass" data-reveal>Contact</x-eyebrow>
            <h1 class="display mt-8 text-display-2" data-split data-split-immediate data-delay="0.25">Request a <em class="text-brass-soft">3D printing quote.</em></h1>
            <p class="mt-8 max-w-md leading-relaxed text-bone/60" data-reveal data-delay="0.6">Send a 3D file, drawing, sketch, photograph or physical sample, with the intended application and quantity.@if ($phone) You can also call or WhatsApp us on {{ $phone }} — files can be shared on WhatsApp.@endif</p>
        </div>

        <dl class="relative mt-16 space-y-7 border-t border-bone/10 pt-10" data-reveal data-delay="0.75">
            @foreach ([['phone', 'Phone / WhatsApp', 'phone'], ['address', 'Studio', 'map-pin'], ['email', 'Email', 'mail'], ['hours', 'Business hours', 'clock']] as [$key, $label, $icon])
                <div class="grid grid-cols-[2rem_1fr] gap-3">
                    <x-icon :name="$icon" class="mt-1 text-lg text-brass" />
                    <div>
                        <dt class="mono text-bone/40">{{ $label }}</dt>
                        <dd class="mt-1.5 text-[15px]"><x-contact-detail :key="$key" :label="$label" /></dd>
                    </div>
                </div>
            @endforeach
            <div class="pt-4">
                <dt class="mono text-bone/40">Follow</dt>
                <dd class="mt-4"><x-social-links /></dd>
            </div>
        </dl>
    </div>

    {{-- Enquiry form (UI only — submission is wired up in the backend phase) --}}
    <div id="enquiry" class="scroll-mt-0 px-5 pt-16 pb-20 sm:px-8 lg:col-span-7 lg:px-16 lg:pt-[calc(var(--header-h)+4rem)] xl:px-24" x-data="contactForm">
        <div x-show="!submitted">
            <p class="mono text-smoke">Get a quote</p>
            <h2 class="display mt-4 text-display-4">Tell us about your project</h2>

            <form class="mt-12 grid gap-x-10 gap-y-9 sm:grid-cols-2" novalidate @submit.prevent="submit">
                @foreach ([['name', 'Your name', 'text', 'name'], ['email', 'Email address', 'email', 'email'], ['phone', 'Phone (optional)', 'tel', 'tel']] as [$field, $label, $type, $autocomplete])
                    <div @class(['sm:col-span-2' => $field === 'phone'])>
                        <label class="field">
                            <input type="{{ $type }}" name="{{ $field }}" x-model="form.{{ $field }}" placeholder=" " autocomplete="{{ $autocomplete }}"
                                   class="field-input peer" :aria-invalid="!!errors.{{ $field }}" aria-describedby="{{ $field }}-error">
                            <span class="field-label">{{ $label }}</span>
                        </label>
                        <p id="{{ $field }}-error" class="field-error" x-show="errors.{{ $field }}" x-text="errors.{{ $field }}" x-cloak></p>
                    </div>
                @endforeach

                <div class="sm:col-span-2">
                    <fieldset>
                        <legend class="mono text-smoke">Service required</legend>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (['FDM 3D printing', 'Rapid prototyping', 'Small-batch production', 'CAD design', 'Parts restoration', 'Automotive prototyping', 'IoT enclosure', 'Statue / model', 'Gifts & miniatures', 'Painting & finishing', 'Something else'] as $subject)
                                <label class="cursor-pointer">
                                    <input type="radio" name="subject" value="{{ $subject }}" x-model="form.subject" class="peer sr-only">
                                    <span class="block rounded-full px-4 py-2.5 text-sm ring-1 ring-ink/15 transition peer-checked:bg-ink peer-checked:text-paper peer-focus-visible:outline peer-focus-visible:outline-brass hover:ring-ink/40">{{ $subject }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <p class="field-error" x-show="errors.subject" x-text="errors.subject" x-cloak></p>
                </div>

                <div class="sm:col-span-2">
                    <label class="field">
                        <textarea name="message" rows="5" x-model="form.message" placeholder=" " class="field-input peer resize-none" :aria-invalid="!!errors.message" aria-describedby="message-error"></textarea>
                        <span class="field-label">Project description, material, quantity &amp; deadline</span>
                    </label>
                    <p id="message-error" class="field-error" x-show="errors.message" x-text="errors.message" x-cloak></p>
                </div>

                {{-- Honeypot for spam bots; hidden from people and screen readers. --}}
                <input type="text" name="website" x-model="form.website" tabindex="-1" autocomplete="off" aria-hidden="true" class="hidden">

                <div class="flex flex-col gap-6 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-xs text-xs leading-relaxed text-smoke" x-show="!failed">By sending this form you agree to be contacted about your enquiry.</p>
                    <p class="max-w-xs text-xs leading-relaxed text-[#a5503f]" x-show="failed" x-cloak>Sorry, your request could not be sent. Please try again{{ $phone ? ', or call / WhatsApp '.$phone : '' }}.</p>
                    <button type="submit" class="btn btn-dark btn-lg" :disabled="sending">
                        <span x-text="sending ? 'Sending…' : 'Request a Quote'">Request a Quote</span>
                        <span class="btn-icon"><x-icon name="send" /></span>
                    </button>
                </div>
            </form>
        </div>

        <div x-cloak x-show="submitted" x-transition.opacity.duration.700ms class="flex min-h-[60vh] flex-col justify-center">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-ink text-xl text-paper"><x-icon name="check" /></span>
            <h2 class="display mt-8 text-display-3">Thank you, <span x-text="form.name.split(' ')[0]"></span>.</h2>
            <p class="mt-6 max-w-md leading-relaxed text-ink/65">Your request has been sent. We’ll review it and get back to you.@if ($whatsapp) To share drawings, photos or CAD files, <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="link-line is-static text-ink">WhatsApp us on {{ $phone }}</a>.@endif</p>
            <button type="button" @click="restart()" class="link-line is-static mt-10 self-start text-sm font-semibold tracking-[0.12em] uppercase">Start a new request <x-icon name="rotate-ccw" /></button>
        </div>
    </div>
</section>

{{-- Map placeholder --}}
<section class="relative h-[420px] overflow-hidden bg-bone lg:h-[520px]" aria-label="Map">
    <svg aria-hidden="true" class="absolute inset-0 h-full w-full text-ink/[0.08]" preserveAspectRatio="xMidYMid slice" viewBox="0 0 1600 520" fill="none" stroke="currentColor">
        <defs><pattern id="map-grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M40 0H0V40" stroke-width="1"/></pattern></defs>
        <rect width="1600" height="520" fill="url(#map-grid)" stroke="none"/>
        <path d="M-20 360C200 330 340 410 560 380S900 240 1120 280 1460 210 1640 240" stroke-width="22" class="text-ink/[0.06]" stroke="currentColor"/>
        <path d="M300 -20L420 540M980 -20L860 540M-20 160L1640 120M1240 -20L1320 540" stroke-width="10"/>
        <path d="M120 -20L200 540M640 -20L700 540M-20 460L1640 430" stroke-width="4"/>
    </svg>
    <div class="relative flex h-full flex-col items-center justify-center px-5 text-center">
        <span class="relative flex h-4 w-4"><span class="absolute inset-0 animate-pulse-ring rounded-full bg-brass"></span><span class="relative h-4 w-4 rounded-full border-2 border-paper bg-ink"></span></span>
        <p class="display mt-8 text-3xl">Studio location</p>
        <p class="mt-3 max-w-sm text-sm text-ink/60">{{ $address ?: 'Address to be confirmed' }}</p>
        @if ($address)
            <a href="{{ \App\Support\Contact::mapsUrl() }}" target="_blank" rel="noopener" class="link-line is-static mt-5 text-sm font-semibold">Open in Google Maps <x-icon name="arrow-up-right" /></a>
        @endif
    </div>
</section>

@endsection
