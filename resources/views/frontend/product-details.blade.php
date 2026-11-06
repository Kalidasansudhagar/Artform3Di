@extends('layouts.app')

@section('title', $product['name'])
@section('description', $product['excerpt'])
@section('header_theme', 'light')

@section('content')

<section class="bg-paper pt-[calc(var(--header-h)+2rem)] pb-24 lg:pt-[calc(var(--header-h)+3rem)] lg:pb-32">
    <div class="container-x">
        <nav aria-label="Breadcrumb" class="mono flex flex-wrap gap-x-2 text-smoke">
            <a href="{{ route('home') }}" class="hover:text-ink">Home</a><span>/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-ink">Shop</a><span>/</span>
            <a href="{{ route('shop.index', ['category' => $product['category']]) }}" class="hover:text-ink">{{ $product['category_name'] }}</a><span>/</span>
            <span class="text-ink">{{ $product['name'] }}</span>
        </nav>

        <div class="mt-8 grid gap-12 lg:mt-12 lg:grid-cols-12 lg:gap-16" x-data="{ view: 'photos' }">
            {{-- Gallery --}}
            <div class="min-w-0 lg:col-span-7">
                <div class="flex flex-col-reverse gap-4 lg:sticky lg:top-[calc(var(--header-h)+1.5rem)] lg:flex-row" data-gallery>
                    <div class="flex gap-3 lg:w-20 lg:flex-col" role="group" aria-label="Product images">
                        @foreach ($product['images'] as $i => $image)
                            <button type="button" data-thumb @click="view = 'photos'"
                                    class="media-frame aspect-[4/5] w-20 opacity-50 ring-1 ring-transparent transition hover:opacity-100 [&.is-active]:opacity-100 [&.is-active]:ring-ink lg:w-full"
                                    aria-label="Show image {{ $i + 1 }}">
                                <img src="{{ asset($image) }}" alt="" width="1200" height="1500" loading="lazy" decoding="async">
                            </button>
                        @endforeach
                        @if ($product['model'])
                            <button type="button" @click="view = 'model'" :class="view === 'model' && 'ring-ink opacity-100'"
                                    class="flex aspect-[4/5] w-20 flex-col items-center justify-center gap-1 bg-bone text-xs ring-1 ring-transparent lg:w-full">
                                <x-icon name="rotate-3d" class="text-xl" /> 3D
                            </button>
                        @endif
                    </div>

                    <div class="relative min-w-0 flex-1">
                        <div class="swiper swiper-clip bg-char" x-show="view === 'photos'">
                            <div class="swiper-wrapper">
                                @foreach ($product['images'] as $i => $image)
                                    <div class="swiper-slide">
                                        <img src="{{ asset($image) }}" alt="{{ $product['name'] }} — view {{ $i + 1 }}" width="1200" height="1500"
                                             class="aspect-[4/5] w-full object-cover" @if ($i) loading="lazy" @else fetchpriority="high" @endif decoding="async">
                                    </div>
                                @endforeach
                            </div>
                            <div class="absolute right-4 bottom-4 z-10 flex gap-2">
                                <button type="button" data-prev class="icon-btn bg-paper/90 text-ink backdrop-blur [&.is-disabled]:opacity-40" aria-label="Previous image"><x-icon name="arrow-left" /></button>
                                <button type="button" data-next class="icon-btn bg-paper/90 text-ink backdrop-blur [&.is-disabled]:opacity-40" aria-label="Next image"><x-icon name="arrow-right" /></button>
                            </div>
                        </div>

                        @if ($product['model'])
                            {{-- Interactive 3D preview (model-viewer is loaded only on pages that use it) --}}
                            <model-viewer x-cloak x-show="view === 'model'" src="{{ asset($product['model']) }}" poster="{{ asset($product['image']) }}"
                                          alt="Interactive 3D model of {{ $product['name'] }}" camera-controls auto-rotate shadow-intensity="1" exposure="0.9"
                                          class="block aspect-[4/5] w-full bg-bone"></model-viewer>
                        @endif

                        @if ($product['badge'])
                            <span class="pointer-events-none absolute top-4 left-4 z-10 bg-paper/90 px-3 py-1.5 text-[10px] font-semibold tracking-[0.2em] uppercase">{{ $product['badge'] }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="min-w-0 lg:col-span-5">
                <p class="eyebrow text-smoke" data-reveal>{{ $product['category_name'] }}</p>
                <h1 class="display mt-6 text-display-3" data-split data-split-immediate data-delay="0.2">{{ $product['name'] }}</h1>
                <p class="mt-6 text-2xl tabular-nums" data-reveal data-delay="0.3">{{ $product['price_label'] }}</p>
                <p class="mt-8 leading-relaxed text-ink/70" data-reveal data-delay="0.35">
                    {{ $product['excerpt'] }} Modelled in CAD and printed on our FDM print farm; available colours and finishes are confirmed when you order.
                </p>

                <div x-data="{ finish: 0, qty: 1 }" class="mt-10 space-y-8 border-t border-ink/10 pt-8" data-reveal data-delay="0.4">
                    <fieldset>
                        <legend class="mono text-smoke">Finish</legend>
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach (['Antique Bronze' => '#a8895a', 'Ivory Marble' => '#e8dfcf', 'Graphite' => '#4a4c50', 'Sandstone' => '#c2b4a0'] as $label => $swatch)
                                <button type="button" @click="finish = {{ $loop->index }}" :aria-pressed="finish === {{ $loop->index }}"
                                        :class="finish === {{ $loop->index }} ? 'ring-ink' : 'ring-ink/15 hover:ring-ink/40'"
                                        class="flex items-center gap-2.5 rounded-full py-1.5 pr-4 pl-1.5 text-sm ring-1 transition">
                                    <span class="h-6 w-6 rounded-full ring-1 ring-ink/10" style="background: {{ $swatch }}"></span>{{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="flex h-14 items-center justify-between rounded-full px-2 ring-1 ring-ink/15 sm:w-36">
                            <button type="button" class="icon-btn h-10 w-10" @click="qty = Math.max(1, qty - 1)" aria-label="Decrease quantity"><x-icon name="minus" /></button>
                            <span class="tabular-nums" x-text="qty" aria-live="polite"></span>
                            <button type="button" class="icon-btn h-10 w-10" @click="qty = Math.min(20, qty + 1)" aria-label="Increase quantity"><x-icon name="plus" /></button>
                        </div>
                        @if ($product['price'])
                            <x-button variant="dark" size="lg" icon="shopping-bag" class="flex-1" ::disabled="$store.ui.bagBusy" @click="$store.ui.addToBag({{ Js::from($product['slug']) }}, qty)">Add to Cart</x-button>
                        @else
                            <x-button :href="route('contact').'#enquiry'" variant="dark" size="lg" icon="shopping-bag" class="flex-1">Request a Quote</x-button>
                        @endif
                    </div>
                    <div class="flex gap-3">
                        @if ($product['price'])
                            <x-button variant="outline-dark" size="lg" icon="arrow-right" class="flex-1" ::disabled="$store.ui.bagBusy" @click="$store.ui.buyNow({{ Js::from($product['slug']) }}, qty)">Buy Now</x-button>
                        @else
                            <x-button :href="route('contact').'#enquiry'" variant="outline-dark" size="lg" icon="arrow-right" class="flex-1">Ask About This Piece</x-button>
                        @endif
                        <button type="button" class="icon-btn h-14 w-14 ring-1 ring-ink/15" @click="$store.ui.toggleWish('{{ $product['slug'] }}')" aria-label="Save to wishlist">
                            <x-icon name="heart" ::class="$store.ui.isWished('{{ $product['slug'] }}') && 'fill-current text-[#a5503f]'" />
                        </button>
                    </div>
                    <p class="text-sm text-ink/55">Need a custom size, colour or personalisation? <a href="{{ route('contact') }}#enquiry" class="link-line is-static text-ink">Request a quote</a></p>
                </div>

                {{-- Accordions --}}
                <div x-data="{ open: 'description' }" class="mt-12 border-t border-ink/10" data-reveal>
                    @php
                        $panels = [
                            'description' => 'Description',
                            'specifications' => 'Specifications',
                            'materials' => 'Material Details',
                            'dimensions' => 'Dimensions',
                        ];
                    @endphp
                    @foreach ($panels as $key => $label)
                        <div class="border-b border-ink/10">
                            <button type="button" @click="open = open === '{{ $key }}' ? null : '{{ $key }}'" :aria-expanded="open === '{{ $key }}'"
                                    class="flex w-full items-center justify-between py-5 text-left">
                                <span class="text-sm font-semibold tracking-[0.14em] uppercase">{{ $label }}</span>
                                <x-icon name="plus" class="transition-transform duration-500" ::class="open === '{{ $key }}' && 'rotate-45'" />
                            </button>
                            <div x-show="open === '{{ $key }}'" x-collapse @if ($key !== 'description') x-cloak @endif>
                                <div class="pb-6 text-[15px] leading-relaxed text-ink/70">
                                    @switch($key)
                                        @case('description')
                                            @if (! empty($product['description']))
                                                <p class="whitespace-pre-line">{{ $product['description'] }}</p>
                                            @else
                                                <p>{{ $product['name'] }} is part of our {{ strtolower($product['category_name']) }} range. It is modelled in CAD and printed layer by layer on our FDM print farm in Sholinganallur, Chennai. Painting is available for selected pieces.</p>
                                            @endif
                                            @break
                                        @case('specifications')
                                            <dl class="divide-y divide-ink/10">
                                                @foreach (['Category' => $product['category_name'], 'Process' => 'FDM 3D printing', 'Finish' => 'Confirmed when you order', 'Weight' => $product['weight'], 'Made' => 'To order, in Chennai'] as $term => $value)
                                                    <div class="flex justify-between gap-6 py-2.5"><dt class="text-smoke">{{ $term }}</dt><dd class="text-right text-ink">{{ $value }}</dd></div>
                                                @endforeach
                                            </dl>
                                            @break
                                        @case('materials')
                                            <p>{{ $product['material'] }}.</p>
                                            <p class="mt-3">Material and finish can be discussed before ordering, based on how and where the piece will be used.</p>
                                            @break
                                        @case('dimensions')
                                            <p class="display text-3xl text-ink">{{ $product['dimensions'] }}</p>
                                            <p class="mt-3">Approximate. Custom scales are available on request.</p>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- From model to object --}}
<section class="bg-ink text-bone">
    <div class="container-x grid gap-px py-24 md:grid-cols-2 lg:py-32">
        <div class="md:pr-10">
            <x-eyebrow class="text-brass">Printed layer by layer</x-eyebrow>
            <h2 class="display mt-8 max-w-lg text-display-3" data-split>From CAD model <em>to finished part.</em></h2>
            <p class="mt-8 max-w-md leading-relaxed text-bone/60" data-reveal>Every piece begins as a 3D model, prepared for printing and built layer by layer on our FDM print farm, with painting available for selected parts.</p>
        </div>
        <div class="mt-12 grid grid-cols-2 gap-4 md:mt-0">
            <figure data-reveal>
                <div class="media-frame aspect-[4/5]" data-reveal-image><img src="{{ asset($product['images'][2]) }}" alt="Digital model wireframe" width="1200" height="1500" loading="lazy"></div>
                <figcaption class="mono mt-3 text-bone/45">01 · Digital model</figcaption>
            </figure>
            <figure class="mt-16" data-reveal>
                <div class="media-frame aspect-[4/5]" data-reveal-image><img src="{{ asset($product['images'][0]) }}" alt="Finished sculpture" width="1200" height="1500" loading="lazy"></div>
                <figcaption class="mono mt-3 text-bone/45">02 · Finished piece</figcaption>
            </figure>
        </div>
    </div>
</section>

{{-- Related products --}}
<section class="section overflow-x-clip bg-paper" data-slider="products">
    <div class="container-x">
        <div class="flex items-end justify-between gap-6">
            <h2 class="display text-display-3" data-split>You may <em>also like</em></h2>
            <div class="flex shrink-0 gap-2">
                <button type="button" data-prev class="icon-btn ring-1 ring-ink/15 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Previous"><x-icon name="arrow-left" /></button>
                <button type="button" data-next class="icon-btn ring-1 ring-ink/15 [&.is-disabled]:pointer-events-none [&.is-disabled]:opacity-30" aria-label="Next"><x-icon name="arrow-right" /></button>
            </div>
        </div>
        <div class="swiper mt-12">
            <div class="swiper-wrapper">
                @foreach ($related as $item)
                    <div class="swiper-slide"><x-product-card :product="$item" /></div>
                @endforeach
            </div>
        </div>
        <div class="slider-progress mt-12 text-ink"><span></span></div>
    </div>
</section>

@endsection
