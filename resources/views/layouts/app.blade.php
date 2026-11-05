<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@hasSection('title')@yield('title') — ARTFORM 3Di @else ARTFORM 3Di — Where Design Meets Technology @endif</title>
    <meta name="description" content="@yield('description', 'ARTFORM 3Di — premium 3D art, creative manufacturing and drone engineering. Transforming imagination into extraordinary creations.')">
    <meta name="theme-color" content="#0c0c0d">
    <meta property="og:title" content="@yield('title', 'ARTFORM 3Di')">
    <meta property="og:description" content="@yield('description', config('artform.tagline'))">
    <meta property="og:type" content="website">
    <link rel="icon" href="{{ asset('assets/icons/favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('assets/icons/favicon.png') }}">

    <script>
        document.documentElement.classList.replace('no-js', 'js');
        // ?motion=off disables animation (useful for QA and screenshots).
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && !/[?&]motion=off/.test(location.search)) {
            document.documentElement.classList.add('motion');
        }
    </script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body x-data x-effect="document.documentElement.classList.toggle('overflow-hidden', $store.ui.locked)" @keydown.escape.window="$store.ui.closeAll()" class="@yield('body_class', 'bg-paper')">
    <a href="#main" class="skip-link">Skip to content</a>
    <div class="page-curtain" aria-hidden="true"></div>

    <x-header :theme="trim($__env->yieldContent('header_theme', 'dark'))" :solid="$__env->hasSection('header_solid')" />

    <main id="main">
        @yield('content')
    </main>

    <x-footer />

    <x-search-overlay />
    <x-quick-view />
    <x-account-drawer />
    <x-toast />

    @stack('scripts')
</body>
</html>
