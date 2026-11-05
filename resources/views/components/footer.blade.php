<footer class="relative overflow-hidden bg-ink text-bone">
    <div class="container-x pt-24 pb-10 lg:pt-32">
        <div class="grid gap-14 lg:grid-cols-12 lg:gap-10">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" aria-label="ARTFORM 3Di — home"><x-logo tone="light" size="lg" /></a>
                <p class="mt-8 max-w-sm text-[15px] leading-relaxed text-bone/60">
                    Premium 3D art, creative manufacturing and engineering innovation — transforming imagination into extraordinary creations, layer by layer.
                </p>
                <x-button :href="route('contact')" variant="outline-light" size="sm" class="mt-8">Start a project</x-button>
            </div>

            <nav class="lg:col-span-2" aria-label="Footer">
                <h2 class="mono text-bone/45">Explore</h2>
                <ul class="mt-6 space-y-3.5 text-[15px]">
                    <li><a class="link-line" href="{{ route('home') }}">Home</a></li>
                    <li><a class="link-line" href="{{ route('shop.index') }}">Shop</a></li>
                    <li><a class="link-line" href="{{ route('about') }}">About Us</a></li>
                    <li><a class="link-line" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </nav>

            <div class="lg:col-span-3">
                <h2 class="mono text-bone/45">Services</h2>
                <ul class="mt-6 space-y-3.5 text-[15px]">
                    <li><a class="link-line" href="{{ route('services') }}#rapid-prototyping">Rapid Prototyping</a></li>
                    <li><a class="link-line" href="{{ route('services') }}#custom-modelling">Custom 3D Modelling</a></li>
                    <li><a class="link-line" href="{{ route('services') }}#art-decor">Premium Art &amp; Decor</a></li>
                    <li><a class="link-line" href="{{ route('drones') }}">Drone Engineering</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="mono text-bone/45">Studio</h2>
                <ul class="mt-6 space-y-3.5 text-[15px] text-bone/80">
                    <li><x-contact-detail key="email" label="Email" /></li>
                    <li><x-contact-detail key="phone" label="Phone" /></li>
                    <li><x-contact-detail key="address" label="Address" /></li>
                </ul>
                <x-social-links class="mt-8" />
            </div>
        </div>

        {{-- Oversized wordmark --}}
        <p aria-hidden="true" class="display pointer-events-none mt-24 text-center text-[14vw] leading-[0.8] tracking-[-0.04em] whitespace-nowrap text-bone/[0.06] select-none lg:mt-32">
            ARTFORM <em class="text-brass/20">3Di</em>
        </p>

        <div class="mt-10 flex flex-col gap-4 border-t border-bone/10 pt-8 text-xs text-bone/45 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} ARTFORM 3Di. All rights reserved.</p>
            <p class="mono">Art · Technology · Innovation</p>
            <a href="#main" class="inline-flex items-center gap-2 transition hover:text-bone" data-no-transition>Back to top <x-icon name="arrow-up" /></a>
        </div>
    </div>
</footer>
