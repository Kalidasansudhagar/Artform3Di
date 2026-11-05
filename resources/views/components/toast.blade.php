<div aria-live="polite" class="pointer-events-none fixed inset-x-0 bottom-0 z-[80] flex justify-center p-4 sm:justify-end sm:p-8">
    <div x-cloak x-show="$store.ui.toast"
         x-transition:enter="transition duration-500 ease-[var(--ease-expo)]" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="pointer-events-auto flex max-w-sm items-start gap-4 bg-ink px-5 py-4 text-sm text-bone shadow-2xl ring-1 ring-bone/10">
        <x-icon name="sparkles" class="mt-0.5 text-base text-brass" />
        <p x-text="$store.ui.toast" class="leading-relaxed"></p>
        <button type="button" class="-mr-1 opacity-50 transition hover:opacity-100" @click="$store.ui.toast = null" aria-label="Dismiss"><x-icon name="x" /></button>
    </div>
</div>
