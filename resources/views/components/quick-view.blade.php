{{-- Quick view modal, populated from the product passed to $store.ui.quickView. --}}
<div x-cloak x-show="$store.ui.quickView" class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="quick-view-title">
    <div x-show="$store.ui.quickView" x-transition.opacity.duration.400ms class="absolute inset-0 bg-ink/75 backdrop-blur-sm" @click="$store.ui.quickView = null"></div>

    <div x-show="$store.ui.quickView"
         x-transition:enter="transition duration-700 ease-[var(--ease-expo)]" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         x-trap="$store.ui.quickView"
         class="relative grid max-h-[92svh] w-full max-w-5xl overflow-y-auto bg-paper text-ink sm:grid-cols-2">
        <button type="button" class="icon-btn absolute top-3 right-3 z-10 bg-paper/80 backdrop-blur" @click="$store.ui.quickView = null" aria-label="Close quick view"><x-icon name="x" /></button>

        <div class="aspect-[4/5] bg-char sm:aspect-auto">
            <img :src="$store.ui.quickView?.image_url" :alt="$store.ui.quickView?.name" class="h-full w-full object-cover" width="1200" height="1500">
        </div>

        <div class="flex flex-col p-6 sm:p-10">
            <p class="eyebrow text-smoke" x-text="$store.ui.quickView?.category_name"></p>
            <h2 id="quick-view-title" class="display mt-4 text-4xl lg:text-5xl" x-text="$store.ui.quickView?.name"></h2>
            <p class="mt-4 text-xl" x-text="$store.ui.quickView?.price_label"></p>
            <p class="mt-6 leading-relaxed text-ink/70" x-text="$store.ui.quickView?.excerpt"></p>

            <dl class="mt-8 divide-y divide-ink/10 border-y border-ink/10 text-sm">
                <div class="flex justify-between gap-6 py-3"><dt class="text-smoke">Material</dt><dd class="text-right" x-text="$store.ui.quickView?.material"></dd></div>
                <div class="flex justify-between gap-6 py-3"><dt class="text-smoke">Dimensions</dt><dd class="text-right" x-text="$store.ui.quickView?.dimensions"></dd></div>
            </dl>

            <div class="mt-auto flex flex-col gap-3 pt-10 sm:flex-row">
                <x-button variant="dark" icon="shopping-bag" class="flex-1" @click="$store.ui.addToBag($store.ui.quickView.slug)">Add to bag</x-button>
                <a :href="$store.ui.quickView?.url" class="btn btn-outline-dark flex-1"><span>View details</span><span class="btn-icon"><x-icon name="arrow-up-right" /></span></a>
            </div>
        </div>
    </div>
</div>
