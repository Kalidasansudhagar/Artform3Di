@extends('layouts.app')

@section('title', 'Shop')
@section('description', 'Products designed and 3D printed by Artform 3Di in Chennai — statues, miniatures, personalised gifts and custom creations.')
@section('header_theme', 'light')

@section('content')

<div x-data="shop({ products: @js($products->values()), categories: @js($categories), priceCeiling: {{ $priceCeiling }} })">

    {{-- Introduction --}}
    <section class="bg-paper pt-[calc(var(--header-h)+4rem)] pb-16 lg:pt-[calc(var(--header-h)+6rem)] lg:pb-24">
        <div class="container-x">
            <nav aria-label="Breadcrumb" class="mono text-smoke" data-reveal>
                <a href="{{ route('home') }}" class="hover:text-ink">Home</a> <span class="mx-2">/</span> <span class="text-ink">Shop</span>
            </nav>
            <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:items-end">
                <h1 class="display text-display-1 lg:col-span-8" data-split data-split-immediate data-delay="0.2">The <em>Collection</em></h1>
                <div class="lg:col-span-4" data-reveal data-delay="0.5">
                    <p class="leading-relaxed text-ink/65">Products designed and 3D printed in our Sholinganallur studio. To order, send an enquiry or message us on WhatsApp.</p>
                    <p class="mono mt-6 text-smoke"><span x-text="results.length"></span> of {{ $products->count() }} pieces</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured collections --}}
    <section class="bg-paper pb-20 lg:pb-28" aria-label="Featured collections">
        <div class="container-x">
            <div class="-mx-5 flex snap-x snap-mandatory gap-4 overflow-x-auto px-5 pb-2 [scrollbar-width:none] sm:-mx-8 sm:px-8 md:mx-0 md:grid md:grid-cols-3 md:gap-6 md:overflow-visible md:px-0">
                @foreach ($collections as $i => $collection)
                    <button type="button" @click="selectCategory('{{ $collection['category'] }}')"
                            class="group relative isolate block aspect-[4/5] w-[78vw] shrink-0 snap-start overflow-hidden bg-char text-left text-bone sm:w-[46vw] md:w-auto"
                            data-reveal data-delay="{{ $i * 0.1 }}">
                        <img src="{{ asset($collection['image']) }}" alt="" width="1200" height="1500" loading="lazy" decoding="async"
                             class="absolute inset-0 -z-10 h-full w-full object-cover transition-transform duration-[1400ms] ease-[var(--ease-expo)] group-hover:scale-105">
                        <span class="absolute inset-0 -z-10 bg-gradient-to-t from-ink/85 via-ink/10 to-transparent"></span>
                        <span class="flex h-full flex-col justify-end p-6 lg:p-8">
                            <span class="mono text-brass-soft">Collection 0{{ $i + 1 }}</span>
                            <span class="display mt-3 text-3xl lg:text-4xl">{{ $collection['title'] }}</span>
                            <span class="mt-2 max-w-xs text-sm text-bone/65">{{ $collection['text'] }}</span>
                            <span class="mt-6 inline-flex items-center gap-2 text-xs font-semibold tracking-[0.16em] uppercase">View pieces <x-icon name="arrow-down-right" class="transition group-hover:translate-x-1" /></span>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Toolbar: categories, search, sort, price --}}
    <div id="catalogue" :style="{ top: $store.ui.headerHidden ? '0px' : 'var(--header-h)' }" class="sticky top-[var(--header-h)] z-30 scroll-mt-[var(--header-h)] border-y transition-[top] duration-500 ease-[var(--ease-expo)] border-ink/10 bg-paper/90 backdrop-blur-xl">
        <div class="container-x">
            <div class="flex items-center gap-4 py-3 lg:gap-8">
                <div class="-mx-1 flex min-w-0 flex-1 gap-1 overflow-x-auto px-1 [scrollbar-width:none]" role="tablist" aria-label="Categories">
                    <button type="button" role="tab" :aria-selected="category === 'all'" @click="category = 'all'"
                            :class="category === 'all' ? 'bg-ink text-paper' : 'hover:bg-ink/5'"
                            class="shrink-0 rounded-full px-4 py-2 text-xs font-medium tracking-wide transition">All</button>
                    @foreach ($categories as $slug => $name)
                        <button type="button" role="tab" :aria-selected="category === '{{ $slug }}'" @click="category = '{{ $slug }}'"
                                :class="category === '{{ $slug }}' ? 'bg-ink text-paper' : 'hover:bg-ink/5'"
                                class="shrink-0 rounded-full px-4 py-2 text-xs font-medium tracking-wide whitespace-nowrap transition">{{ $name }}</button>
                    @endforeach
                </div>

                <label class="relative hidden w-56 items-center lg:flex">
                    <span class="sr-only">Search products</span>
                    <x-icon name="search" class="absolute left-0 text-smoke" />
                    <input x-model.debounce.200ms="query" type="search" placeholder="Search pieces" class="w-full border-b border-ink/15 bg-transparent py-2 pl-7 text-sm outline-none focus:border-ink">
                </label>

                <button type="button" @click="filtersOpen = !filtersOpen" :aria-expanded="filtersOpen" aria-controls="filter-panel"
                        class="relative flex shrink-0 items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold tracking-[0.12em] uppercase ring-1 ring-ink/15 transition hover:ring-ink">
                    <x-icon name="sliders-horizontal" /> <span class="hidden sm:inline">Filter &amp; sort</span>
                    <span x-cloak x-show="activeFilters" x-text="activeFilters" class="bag-count static ml-1"></span>
                </button>
            </div>

            <div id="filter-panel" x-cloak x-show="filtersOpen" x-collapse>
                <div class="grid gap-8 border-t border-ink/10 py-8 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="block lg:hidden">
                        <span class="mono text-smoke">Search</span>
                        <input x-model.debounce.200ms="query" type="search" placeholder="Search pieces" class="mt-3 w-full border-b border-ink/20 bg-transparent py-2 text-base outline-none focus:border-ink">
                    </label>
                    <label class="block">
                        <span class="mono text-smoke">Sort by</span>
                        <span class="relative mt-3 block">
                            <select x-model="sort" class="w-full appearance-none border-b border-ink/20 bg-transparent py-2 pr-8 text-base outline-none focus:border-ink">
                                <option value="featured">Featured</option>
                                <option value="price-asc">Price: low to high</option>
                                <option value="price-desc">Price: high to low</option>
                                <option value="name">Name: A–Z</option>
                            </select>
                            <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-0 -translate-y-1/2" />
                        </span>
                    </label>
                    <div class="sm:col-span-2 lg:col-span-2">
                        <div class="flex items-baseline justify-between">
                            <span class="mono text-smoke" id="price-label">Max price</span>
                            <span class="text-sm tabular-nums" x-text="'Up to ' + formatPrice(maxPrice)"></span>
                        </div>
                        <input type="range" class="range mt-6" min="5000" :max="priceCeiling" step="500" x-model.number="maxPrice" aria-labelledby="price-label">
                        <div class="mt-3 flex justify-between text-xs text-smoke"><span>₹5,000</span><span x-text="formatPrice(priceCeiling)"></span></div>
                    </div>
                    <div class="flex items-end gap-3 sm:col-span-2 lg:col-span-4">
                        <button type="button" @click="reset()" class="link-line text-xs font-semibold tracking-[0.14em] uppercase">Clear all</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Product grid --}}
    <section class="bg-paper py-14 lg:py-20" aria-label="Products">
        <div class="container-x">
            <div class="grid grid-cols-1 gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-8 lg:gap-y-20">
                @foreach ($products as $i => $product)
                    <div x-show="isVisible('{{ $product['slug'] }}')" :style="{ order: position('{{ $product['slug'] }}') }"
                         x-transition:enter="transition duration-700 ease-[var(--ease-expo)]" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0">
                        <x-product-card :product="$product" :eager="$i < 3" />
                    </div>
                @endforeach
            </div>

            {{-- Empty state --}}
            <div x-cloak x-show="results.length === 0" class="mx-auto max-w-md py-24 text-center">
                <p class="display text-4xl">Nothing matches — yet.</p>
                <p class="mt-4 text-ink/60">Try another search or category, or ask us about a custom print.</p>
                <div class="mt-8 flex justify-center gap-3">
                    <x-button variant="dark" icon="rotate-ccw" @click="reset()">Reset filters</x-button>
                    <x-button :href="route('contact')" variant="outline-dark">Get a Quote</x-button>
                </div>
            </div>

            {{-- Pagination (design only — single page of placeholder products) --}}
            <nav x-show="results.length" class="mt-24 flex items-center justify-between border-t border-ink/10 pt-8" aria-label="Pagination">
                <span class="flex items-center gap-2 text-xs font-semibold tracking-[0.14em] text-ink/30 uppercase" aria-disabled="true"><x-icon name="arrow-left" /> Previous</span>
                <ol class="flex items-center gap-1 text-sm">
                    <li><span aria-current="page" class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-paper">1</span></li>
                    <li><span class="flex h-10 w-10 items-center justify-center rounded-full text-ink/30" title="More pieces coming soon">2</span></li>
                    <li><span class="flex h-10 w-10 items-center justify-center rounded-full text-ink/30" title="More pieces coming soon">3</span></li>
                </ol>
                <span class="flex items-center gap-2 text-xs font-semibold tracking-[0.14em] text-ink/30 uppercase" aria-disabled="true">Next <x-icon name="arrow-right" /></span>
            </nav>
        </div>
    </section>
</div>

{{-- Commission band --}}
<section class="bg-ink text-bone">
    <div class="container-x grid gap-10 py-20 lg:grid-cols-12 lg:items-center lg:py-28">
        <div class="lg:col-span-7">
            <x-eyebrow class="text-brass">Custom Orders</x-eyebrow>
            <h2 class="display mt-6 text-display-3" data-split>Looking for something <em>made to your design?</em></h2>
        </div>
        <div class="lg:col-span-4 lg:col-start-9" data-reveal>
            <p class="leading-relaxed text-bone/60">Custom statues, miniatures, personalised gifts and models — printed from your references or designs, with painting available for selected parts.</p>
            <x-button :href="route('contact').'#enquiry'" variant="light" class="mt-8">Request a Quote</x-button>
        </div>
    </div>
</section>

@endsection
