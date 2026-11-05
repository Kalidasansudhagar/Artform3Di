{{-- Theme describes the page area behind the header: "dark" heroes get light text, "light" pages get dark text. --}}
@props(['theme' => 'dark', 'solid' => false])

@php
    $links = [
        ['label' => 'Home', 'route' => 'home', 'match' => 'home'],
        ['label' => 'Shop', 'route' => 'shop.index', 'match' => 'shop.*'],
        ['label' => '3D Printing', 'route' => 'services', 'match' => 'services'],
        ['label' => 'Drones', 'route' => 'drones', 'match' => 'drones'],
        ['label' => 'About Us', 'route' => 'about', 'match' => 'about'],
        ['label' => 'Contact', 'route' => 'contact', 'match' => 'contact'],
    ];
@endphp

<header x-data="siteHeader" :class="{ 'is-scrolled': scrolled || $store.ui.menuOpen || {{ $solid ? 'true' : 'false' }}, 'is-hidden': hidden }" class="site-header site-header--{{ $theme }}">
    <div class="container-x flex h-[var(--header-h)] items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="relative z-10" aria-label="ARTFORM 3Di — home">
            <x-logo :tone="$theme === 'light' ? 'dark' : 'light'" />
        </a>

        <nav class="hidden items-center gap-9 lg:flex" aria-label="Primary">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['match']); @endphp
                <a href="{{ route($link['route']) }}" @class(['nav-link', 'is-active' => $active]) @if ($active) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="-mr-2 flex items-center">
            <button type="button" class="icon-btn" @click="$store.ui.openSearch()" aria-label="Search">
                <x-icon name="search" />
            </button>
            <button type="button" class="icon-btn hidden sm:inline-flex" @click="$store.ui.account()" aria-label="Account">
                <x-icon name="user-round" />
            </button>
            <button type="button" class="icon-btn" @click="$store.ui.notify($store.ui.bagCount ? 'Your bag is saved for this visit. Online checkout opens soon.' : 'Your bag is empty — explore the collection.')" aria-label="Shopping bag">
                <x-icon name="shopping-bag" />
                <span x-cloak x-show="$store.ui.bagCount" x-text="$store.ui.bagCount" class="bag-count"></span>
            </button>
            <button type="button" class="icon-btn lg:hidden" @click="$store.ui.menuOpen = true" aria-label="Open menu" :aria-expanded="$store.ui.menuOpen" aria-controls="mobile-menu">
                <x-icon name="menu" />
            </button>
        </div>
    </div>
</header>

{{-- Mobile navigation --}}
<div id="mobile-menu" x-cloak x-show="$store.ui.menuOpen" class="fixed inset-0 z-[60] lg:hidden" role="dialog" aria-modal="true" aria-label="Menu">
    <div x-show="$store.ui.menuOpen" x-transition.opacity.duration.500ms class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="$store.ui.menuOpen = false"></div>

    <div x-show="$store.ui.menuOpen"
         x-transition:enter="transition duration-700 ease-[var(--ease-expo)]" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition duration-500 ease-[var(--ease-silk)]" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         x-trap="$store.ui.menuOpen"
         class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-ink text-bone">
        <div class="flex h-[var(--header-h)] items-center justify-between px-5 sm:px-8">
            <x-logo tone="light" />
            <button type="button" class="icon-btn -mr-2" @click="$store.ui.menuOpen = false" aria-label="Close menu">
                <x-icon name="x" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-5 pt-6 sm:px-8" aria-label="Mobile">
            <ul class="border-t border-bone/10">
                @foreach ($links as $i => $link)
                    @php $active = request()->routeIs($link['match']); @endphp
                    <li class="border-b border-bone/10"
                        x-show="$store.ui.menuOpen"
                        x-transition:enter="transition duration-700 ease-[var(--ease-expo)]"
                        x-transition:enter-start="opacity-0 translate-x-8"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        style="transition-delay: {{ 120 + $i * 60 }}ms">
                        <a href="{{ route($link['route']) }}" @if ($active) aria-current="page" @endif
                           class="group flex items-baseline gap-5 py-5">
                            <span class="font-mono text-[11px] text-brass">0{{ $i + 1 }}</span>
                            <span @class(['display text-4xl transition-colors', 'text-brass-soft' => $active])>{{ $link['label'] }}</span>
                            <x-icon name="arrow-up-right" class="ml-auto self-center text-lg opacity-40 transition group-hover:rotate-45 group-hover:opacity-100" />
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="space-y-5 px-5 pt-8 pb-[max(2rem,env(safe-area-inset-bottom))] text-sm text-bone/70 sm:px-8">
            <div class="flex gap-3">
                <button type="button" class="btn btn-outline-light btn-sm flex-1" @click="$store.ui.openSearch()"><x-icon name="search" /> <span>Search</span></button>
                <button type="button" class="btn btn-outline-light btn-sm flex-1" @click="$store.ui.account()"><x-icon name="user-round" /> <span>Account</span></button>
            </div>
            <x-contact-detail key="email" label="Email" class="block" />
            <x-social-links class="text-bone" />
        </div>
    </div>
</div>
