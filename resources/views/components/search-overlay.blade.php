{{-- Search submits to the shop, which filters the catalogue client-side from the ?q= parameter. --}}
<div x-cloak x-show="$store.ui.searchOpen" class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Search">
    <div x-show="$store.ui.searchOpen" x-transition.opacity.duration.400ms class="absolute inset-0 bg-ink/70 backdrop-blur-md" @click="$store.ui.searchOpen = false"></div>

    <div x-show="$store.ui.searchOpen"
         x-transition:enter="transition duration-700 ease-[var(--ease-expo)]" x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition duration-400 ease-[var(--ease-silk)]" x-transition:leave-start="translate-y-0" x-transition:leave-end="-translate-y-full"
         x-trap="$store.ui.searchOpen"
         class="relative bg-paper text-ink">
        <div class="container-x py-8 lg:py-14">
            <div class="flex items-center justify-between">
                <p class="eyebrow text-smoke">Search the collection</p>
                <button type="button" class="icon-btn -mr-2" @click="$store.ui.searchOpen = false" aria-label="Close search"><x-icon name="x" /></button>
            </div>

            <form action="{{ route('shop.index') }}" method="GET" role="search" class="mt-6 flex items-end gap-4 border-b border-ink/20 pb-3 focus-within:border-ink">
                <label for="site-search" class="sr-only">Search products</label>
                <input id="site-search" name="q" type="search" autocomplete="off" placeholder="Sculptures, deities, vessels…"
                       class="display w-full bg-transparent text-3xl outline-none placeholder:text-smoke/60 sm:text-5xl lg:text-6xl">
                <button type="submit" class="icon-btn shrink-0 text-2xl" aria-label="Submit search"><x-icon name="arrow-right" /></button>
            </form>

            <div class="mt-8 flex flex-wrap items-center gap-2">
                <span class="mr-2 text-xs text-smoke">Popular</span>
                @foreach (\App\Support\Catalog::CATEGORIES as $slug => $name)
                    <a href="{{ route('shop.index', ['category' => $slug]) }}" class="rounded-full px-4 py-2 text-xs ring-1 ring-ink/15 transition hover:bg-ink hover:text-paper">{{ $name }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>
