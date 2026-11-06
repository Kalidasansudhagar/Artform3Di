{{-- Slide-in sign-in panel opened from the header account icon. --}}
<div x-cloak x-show="$store.ui.accountOpen" class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-labelledby="account-drawer-title">
    <div x-show="$store.ui.accountOpen" x-transition.opacity.duration.500ms class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="$store.ui.accountOpen = false"></div>

    <div x-show="$store.ui.accountOpen"
         x-transition:enter="transition duration-700 ease-[var(--ease-expo)]" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition duration-500 ease-[var(--ease-silk)]" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         x-trap="$store.ui.accountOpen"
         class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col overflow-y-auto bg-paper text-ink">
        <div class="flex h-[var(--header-h)] shrink-0 items-center justify-between border-b border-ink/10 px-6 sm:px-10">
            <p class="eyebrow text-smoke">My Account</p>
            <button type="button" class="icon-btn -mr-2" @click="$store.ui.accountOpen = false" aria-label="Close account panel"><x-icon name="x" /></button>
        </div>

        <div class="flex-1 px-6 py-10 sm:px-10">
            <h2 id="account-drawer-title" class="display text-4xl">Welcome <em>back.</em></h2>
            <p class="mt-3 text-sm leading-relaxed text-ink/60">Sign in to view your orders, saved pieces and commissions.</p>

            <x-login-form id-prefix="drawer-" class="mt-10" />

            <div class="mt-10 border-t border-ink/10 pt-8">
                <p class="text-sm text-ink/70">New to ARTFORM 3Di?</p>
                <x-button :href="route('register')" variant="outline-dark" class="mt-4 w-full">Create an account</x-button>
                <ul class="mt-8 space-y-3 text-sm text-ink/60">
                    @foreach ([['package', 'Track orders and commissions'], ['heart', 'Keep a wishlist of favourite pieces'], ['map-pin', 'Save addresses for faster checkout']] as [$icon, $text])
                        <li class="flex items-center gap-3"><x-icon :name="$icon" class="text-brass" />{{ $text }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
