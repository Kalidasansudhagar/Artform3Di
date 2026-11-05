{{--
    Product tile. Tone "light" sits on warm-white sections, "dark" on charcoal ones.
    Wishlist, quick view and add-to-bag are visual interactions only in this phase.
--}}
@props(['product', 'tone' => 'light', 'eager' => false])

<article {{ $attributes->class(['product-card group relative', 'text-ink' => $tone === 'light', 'text-bone' => $tone === 'dark']) }}>
    <div class="relative">
        <a href="{{ $product['url'] }}" class="product-media media-frame block aspect-[4/5]" aria-label="{{ $product['name'] }}">
            <img src="{{ asset($product['images'][0]) }}" alt="{{ $product['name'] }}" width="1200" height="1500"
                 loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async" class="is-primary absolute inset-0">
            <img src="{{ asset($product['images'][1]) }}" alt="" width="1200" height="1500" loading="lazy" decoding="async" aria-hidden="true"
                 class="is-secondary absolute inset-0">
        </a>

        @if ($product['badge'])
            <span class="pointer-events-none absolute top-4 left-4 bg-paper/90 px-3 py-1.5 text-[10px] font-semibold tracking-[0.2em] text-ink uppercase backdrop-blur">{{ $product['badge'] }}</span>
        @endif

        <button type="button" class="absolute top-3 right-3 flex h-10 w-10 items-center justify-center rounded-full bg-paper/85 text-ink backdrop-blur transition hover:bg-paper"
                @click="$store.ui.toggleWish('{{ $product['slug'] }}')"
                :aria-pressed="$store.ui.isWished('{{ $product['slug'] }}')"
                aria-label="Save {{ $product['name'] }} to wishlist">
            <x-icon name="heart" class="transition" ::class="$store.ui.isWished('{{ $product['slug'] }}') && 'fill-current text-[#a5503f]'" />
        </button>

        <div class="product-actions absolute inset-x-3 bottom-3 flex gap-2">
            <button type="button" class="flex h-11 flex-1 items-center justify-center gap-2 bg-paper/95 text-[11px] font-semibold tracking-[0.16em] text-ink uppercase backdrop-blur transition hover:bg-paper"
                    @click="$store.ui.quickView = @js($product)">
                <x-icon name="eye" class="text-sm" /> Quick view
            </button>
            <button type="button" class="flex h-11 w-11 items-center justify-center bg-ink text-paper transition hover:bg-graphite"
                    @click="$store.ui.addToBag(@js($product['name']))" aria-label="Add {{ $product['name'] }} to bag">
                <x-icon name="plus" />
            </button>
        </div>
    </div>

    <div class="mt-5 flex items-start justify-between gap-4">
        <div>
            <p @class(['text-[11px] tracking-[0.18em] uppercase', 'text-smoke' => $tone === 'light', 'text-bone/50' => $tone === 'dark'])>{{ $product['category_name'] }}</p>
            <h3 class="display mt-2 text-2xl leading-tight lg:text-[1.7rem]">
                <a href="{{ $product['url'] }}" class="bg-[length:0_1px] bg-left-bottom bg-no-repeat transition-[background-size] duration-500 [background-image:linear-gradient(currentColor,currentColor)] group-hover:bg-[length:100%_1px]">{{ $product['name'] }}</a>
            </h3>
        </div>
        <p class="shrink-0 pt-6 text-sm tabular-nums">{{ $product['price_label'] }}</p>
    </div>
</article>
