<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — ARTFORM 3Di Admin</title>
    <link rel="icon" href="{{ asset('assets/icons/favicon.png') }}" type="image/png">
    <script>document.documentElement.classList.replace('no-js', 'js');</script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin bg-[#f3f0ea] text-ink" x-data="{ sidebar: false }" @keydown.escape.window="sidebar = false; $store.ui.closeAll()">
    <a href="#admin-main" class="skip-link">Skip to content</a>

    <div class="min-h-svh lg:grid lg:grid-cols-[272px_minmax(0,1fr)]">
        <x-admin.sidebar />

        <div class="flex min-w-0 flex-col">
            <x-admin.topbar :title="trim($__env->yieldContent('title', 'Dashboard'))" />
            <main id="admin-main" class="flex-1 px-4 py-8 sm:px-8 lg:px-10 lg:py-10">
                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 flex items-center gap-3 rounded-xl bg-[#2f8350]/10 px-4 py-3 text-sm" role="status">
                        <x-icon name="circle-check" class="shrink-0 text-[#2f8350]" />
                        <span class="flex-1">{{ session('status') }}</span>
                        <button type="button" @click="show = false" class="text-smoke hover:text-ink" aria-label="Dismiss"><x-icon name="x" /></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 flex items-center gap-3 rounded-xl bg-[#b5432f]/10 px-4 py-3 text-sm" role="alert">
                        <x-icon name="circle-x" class="shrink-0 text-[#b5432f]" />
                        <span>Please check the highlighted fields and try again.</span>
                    </div>
                @endif
                @yield('content')
            </main>
            <footer class="flex flex-col gap-2 border-t border-ink/10 px-4 py-6 text-xs text-smoke sm:flex-row sm:justify-between sm:px-8 lg:px-10">
                <p>&copy; {{ now()->year }} ARTFORM 3Di · Admin</p>
                <p>Signed in as {{ auth()->user()->email }}</p>
            </footer>
        </div>
    </div>

    <x-toast />
</body>
</html>
