@props(['title' => 'Dashboard'])

@php
    $notifications = [
        ['inbox', 'New enquiry from Anitha P.', 'Custom sculpture · 35 min ago'],
        ['package', 'Order AF-1048 moved to Printing', '2 hours ago'],
        ['clock', 'Order AF-1047 awaiting confirmation', '5 hours ago'],
    ];
@endphp

<header class="sticky top-0 z-30 border-b border-ink/10 bg-[#f3f0ea]/85 backdrop-blur-xl">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-8 lg:h-20 lg:px-10">
        <button type="button" class="icon-btn -ml-2 lg:hidden" @click="sidebar = true" aria-label="Open navigation"><x-icon name="menu" /></button>

        <div class="min-w-0">
            <p class="mono hidden text-[10px] text-smoke sm:block">Admin / {{ $title }}</p>
            <h1 class="truncate text-lg font-semibold tracking-tight sm:text-xl">{{ $title }}</h1>
        </div>

        <form role="search" class="relative ml-auto hidden w-72 md:block xl:w-96" @submit.prevent="$store.ui.notify('Admin search will be connected in the next phase.')">
            <label for="admin-search" class="sr-only">Search the admin</label>
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-smoke" />
            <input id="admin-search" type="search" placeholder="Search orders, products, customers…"
                   class="h-11 w-full rounded-full bg-white/80 pr-4 pl-10 text-sm ring-1 ring-ink/10 outline-none transition placeholder:text-smoke focus:ring-ink/40">
        </form>

        <span class="hidden items-center gap-2 rounded-full bg-[#c08417]/12 px-3 py-1.5 text-xs font-medium text-ink lg:inline-flex" title="All figures on this dashboard are illustrative">
            <x-icon name="flask-conical" class="text-[#c08417]" /> Sample data
        </span>

        <div class="ml-auto flex items-center md:ml-0">
            {{-- Notifications --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" class="icon-btn" @click="open = !open" :aria-expanded="open" aria-label="Notifications">
                    <x-icon name="bell" />
                    <span class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-[#b5432f] ring-2 ring-[#f3f0ea]"></span>
                </button>
                <div x-cloak x-show="open" x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-80 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-ink/10">
                    <p class="border-b border-ink/10 px-4 py-3 text-sm font-semibold">Notifications</p>
                    <ul class="divide-y divide-ink/5">
                        @foreach ($notifications as [$icon, $text, $meta])
                            <li class="flex gap-3 px-4 py-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-bone text-brass"><x-icon :name="$icon" /></span>
                                <div class="min-w-0">
                                    <p class="text-sm">{{ $text }}</p>
                                    <p class="mt-0.5 text-xs text-smoke">{{ $meta }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Profile --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" class="ml-1 flex items-center gap-2 rounded-full p-1 pr-2 transition hover:bg-ink/5" @click="open = !open" :aria-expanded="open" aria-label="Account menu">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink text-sm font-semibold text-bone">A</span>
                    <x-icon name="chevron-down" class="hidden text-smoke sm:block" />
                </button>
                <div x-cloak x-show="open" x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl bg-white py-2 shadow-xl ring-1 ring-ink/10">
                    <div class="border-b border-ink/10 px-4 pt-1 pb-3">
                        <p class="text-sm font-semibold">Admin</p>
                        <p class="text-xs text-smoke">Administrator</p>
                    </div>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-bone"><x-icon name="external-link" class="text-smoke" /> View website</a>
                    <button type="button" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm hover:bg-bone" @click="open = false; $store.ui.notify('Settings arrive in the next phase.')"><x-icon name="settings" class="text-smoke" /> Settings</button>
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[#b5432f] hover:bg-bone"><x-icon name="log-out" /> Sign out</a>
                </div>
            </div>
        </div>
    </div>
</header>
