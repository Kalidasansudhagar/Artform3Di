@props(['title' => 'Dashboard'])

@php
    $latest = \App\Models\Enquiry::query()->where('status', 'new')->latest()->take(5)->get();
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-30 border-b border-ink/10 bg-[#f3f0ea]/85 backdrop-blur-xl">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-8 lg:h-20 lg:px-10">
        <button type="button" class="icon-btn -ml-2 lg:hidden" @click="sidebar = true" aria-label="Open navigation"><x-icon name="menu" /></button>

        <div class="min-w-0">
            <p class="mono hidden text-[10px] text-smoke sm:block">Admin / {{ $title }}</p>
            <h1 class="truncate text-lg font-semibold tracking-tight sm:text-xl">{{ $title }}</h1>
        </div>

        <form role="search" method="GET" action="{{ route('admin.enquiries.index') }}" class="relative ml-auto hidden w-72 md:block xl:w-96">
            <label for="admin-search" class="sr-only">Search enquiries</label>
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-smoke" />
            <input id="admin-search" name="q" type="search" value="{{ request()->routeIs('admin.enquiries.index') ? request('q') : '' }}" placeholder="Search enquiries by name, email or phone…"
                   class="h-11 w-full rounded-full bg-white/80 pr-4 pl-10 text-sm ring-1 ring-ink/10 outline-none transition placeholder:text-smoke focus:ring-ink/40">
        </form>

        <div class="ml-auto flex items-center md:ml-0">
            {{-- New enquiries --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" class="icon-btn" @click="open = !open" :aria-expanded="open" aria-label="New enquiries">
                    <x-icon name="bell" />
                    @if ($latest->isNotEmpty())
                        <span class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-[#b5432f] ring-2 ring-[#f3f0ea]"></span>
                    @endif
                </button>
                <div x-cloak x-show="open" x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-80 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-ink/10">
                    <p class="border-b border-ink/10 px-4 py-3 text-sm font-semibold">New enquiries</p>
                    <ul class="divide-y divide-ink/5">
                        @forelse ($latest as $enquiry)
                            <li>
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="flex gap-3 px-4 py-3 transition hover:bg-bone">
                                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-bone text-brass"><x-icon name="inbox" /></span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm">{{ $enquiry->name }} · {{ $enquiry->subject }}</span>
                                        <span class="mt-0.5 block text-xs text-smoke">{{ $enquiry->created_at->diffForHumans() }}</span>
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="px-4 py-6 text-center text-sm text-smoke">You’re all caught up.</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('admin.enquiries.index') }}" class="block border-t border-ink/10 px-4 py-3 text-center text-xs font-semibold hover:bg-bone">View all enquiries</a>
                </div>
            </div>

            {{-- Profile --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" class="ml-1 flex items-center gap-2 rounded-full p-1 pr-2 transition hover:bg-ink/5" @click="open = !open" :aria-expanded="open" aria-label="Account menu">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink text-sm font-semibold text-bone">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <x-icon name="chevron-down" class="hidden text-smoke sm:block" />
                </button>
                <div x-cloak x-show="open" x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl bg-white py-2 shadow-xl ring-1 ring-ink/10">
                    <div class="border-b border-ink/10 px-4 pt-1 pb-3">
                        <p class="truncate text-sm font-semibold">{{ $user->name }}</p>
                        <p class="truncate text-xs text-smoke">{{ $user->email }}</p>
                    </div>
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-bone"><x-icon name="user-round" class="text-smoke" /> My profile</a>
                    <a href="{{ route('admin.settings.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-bone"><x-icon name="settings" class="text-smoke" /> Settings</a>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-bone"><x-icon name="external-link" class="text-smoke" /> View website</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-[#b5432f] hover:bg-bone"><x-icon name="log-out" /> Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
