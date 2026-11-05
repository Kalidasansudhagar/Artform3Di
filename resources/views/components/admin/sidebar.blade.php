{{-- Admin navigation. Only the dashboard exists in this phase; other sections are listed as "Soon". --}}
@php
    $groups = [
        'Overview' => [
            ['Dashboard', 'layout-dashboard', 'admin.dashboard', null],
        ],
        'Commerce' => [
            ['Orders', 'package', null, '6'],
            ['Products', 'box', null, null],
            ['Customers', 'users', null, null],
        ],
        'Studio' => [
            ['Enquiries', 'inbox', null, '2'],
            ['Projects', 'folder-kanban', null, null],
            ['Services', 'printer', null, null],
        ],
        'Website' => [
            ['Pages & content', 'file-text', null, null],
            ['Settings', 'settings', null, null],
        ],
    ];
@endphp

{{-- Mobile overlay --}}
<div x-cloak x-show="sidebar" x-transition.opacity class="fixed inset-0 z-40 bg-ink/60 backdrop-blur-sm lg:hidden" @click="sidebar = false"></div>

<aside :data-open="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-[272px] flex-col bg-ink text-bone transition-transform duration-500 ease-[var(--ease-expo)] max-lg:-translate-x-full max-lg:data-[open]:translate-x-0 lg:sticky lg:top-0 lg:h-svh"
       aria-label="Admin navigation">
    <div class="flex h-20 shrink-0 items-center justify-between px-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3" aria-label="ARTFORM 3Di admin dashboard">
            <x-logo tone="light" size="sm" />
            <span class="rounded-full bg-bone/10 px-2.5 py-1 text-[10px] font-semibold tracking-[0.2em] text-brass-soft uppercase">Admin</span>
        </a>
        <button type="button" class="icon-btn -mr-2 lg:hidden" @click="sidebar = false" aria-label="Close navigation"><x-icon name="x" /></button>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 pt-4 pb-6 [scrollbar-width:thin]">
        @foreach ($groups as $group => $items)
            <p class="mono mt-6 mb-2 px-3 text-[10px] text-bone/35 first:mt-0">{{ $group }}</p>
            <ul class="space-y-0.5">
                @foreach ($items as [$label, $icon, $route, $badge])
                    <li>
                        @if ($route)
                            @php $active = request()->routeIs($route); @endphp
                            <a href="{{ route($route) }}" @if ($active) aria-current="page" @endif
                               @class([
                                   'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition',
                                   'bg-bone/10 text-bone' => $active,
                                   'text-bone/70 hover:bg-bone/5 hover:text-bone' => ! $active,
                               ])>
                                <x-icon :name="$icon" @class(['text-[17px]', 'text-brass-soft' => $active]) />
                                <span class="flex-1">{{ $label }}</span>
                                @if ($active)<span class="h-1.5 w-1.5 rounded-full bg-brass"></span>@endif
                            </a>
                        @else
                            <span class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-bone/40" aria-disabled="true" title="Available in the next phase">
                                <x-icon :name="$icon" class="text-[17px]" />
                                <span class="flex-1">{{ $label }}</span>
                                @if ($badge)<span class="rounded-full bg-bone/10 px-2 text-[11px] text-bone/60 tabular-nums">{{ $badge }}</span>@endif
                                <span class="text-[9px] font-semibold tracking-[0.16em] uppercase">Soon</span>
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endforeach
    </nav>

    <div class="shrink-0 border-t border-bone/10 p-4">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-bone/70 transition hover:bg-bone/5 hover:text-bone">
            <x-icon name="external-link" class="text-[17px]" /> View website
        </a>
        <div class="mt-3 flex items-center gap-3 rounded-lg bg-bone/5 p-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brass text-sm font-semibold text-ink">A</span>
            <div class="min-w-0 flex-1 leading-tight">
                <p class="truncate text-sm">Admin</p>
                <p class="truncate text-xs text-bone/45">Administrator</p>
            </div>
            <a href="{{ route('login') }}" class="icon-btn h-9 w-9 text-bone/60 hover:text-bone" aria-label="Sign out" title="Sign out"><x-icon name="log-out" /></a>
        </div>
    </div>
</aside>
