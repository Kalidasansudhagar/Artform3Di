@extends('layouts.admin')

@section('title', 'Dashboard')

{{-- Live figures from App\Support\AdminStats. The range filter re-renders the KPI row and the enquiries chart together. --}}

@section('content')
<div x-data="{
        range: @js($defaultRange),
        ranges: @js($ranges),
        kpis: @js($kpis),
        series: @js($series),
        showTable: false,
        greeting() { const h = new Date().getHours(); return h < 12 ? 'Good morning' : h < 17 ? 'Good afternoon' : 'Good evening'; },
        number(v) { return new Intl.NumberFormat('en-IN').format(v); },
        spark(values) {
            const min = Math.min(...values), max = Math.max(...values), span = max - min || 1;
            return values.map((v, i) => `${(i / (values.length - 1) * 116 + 2).toFixed(1)},${(30 - (v - min) / span * 26).toFixed(1)}`).join(' ');
        },
        sparkEnd(values) { return this.spark(values).split(' ').pop().split(','); },
        get total() { return this.series[this.range].reduce((sum, p) => sum + p.value, 0); },
     }"
     x-init="$watch('range', r => window.dispatchEvent(new CustomEvent('admin-range', { detail: r })))">

    {{-- Heading + quick actions --}}
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm text-smoke">{{ now()->format('l, j F Y') }}</p>
            <h2 class="display mt-1 text-4xl lg:text-5xl"><span x-text="greeting()">Welcome</span>, <em>{{ strtok(auth()->user()->name, ' ') }}.</em></h2>
            <p class="mt-2 text-sm text-ink/60">Here’s what’s happening across enquiries, the shop and the website.</p>
        </div>
        <div class="flex gap-2">
            <x-button :href="route('admin.enquiries.index')" variant="outline-dark" size="sm" icon="inbox">Enquiries</x-button>
            <x-button :href="route('admin.products.create')" variant="dark" size="sm" icon="plus">Add product</x-button>
        </div>
    </div>

    {{-- Filters: one row, above everything they scope --}}
    <div class="mt-8 flex flex-wrap items-center gap-3">
        <div class="inline-flex rounded-full bg-white p-1 ring-1 ring-ink/10" role="radiogroup" aria-label="Date range">
            <template x-for="(label, key) in ranges" :key="key">
                <button type="button" role="radio" :aria-checked="range === key" @click="range = key"
                        :class="range === key ? 'bg-ink text-paper' : 'text-ink/70 hover:text-ink'"
                        class="rounded-full px-3.5 py-1.5 text-xs font-medium whitespace-nowrap transition sm:px-4" :aria-label="label">
                    <span class="sm:hidden" x-text="key.toUpperCase()"></span><span class="hidden sm:inline" x-text="label"></span>
                </button>
            </template>
        </div>
        <span class="text-xs text-smoke">Enquiry change is compared with the previous period</span>
    </div>

    {{-- KPI row --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <template x-for="kpi in kpis[range]" :key="kpi.label">
            <article class="rounded-2xl bg-white p-5 ring-1 ring-ink/[0.06] lg:p-6">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm text-ink/60" x-text="kpi.label"></p>
                    <template x-if="kpi.delta !== null">
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                              :class="kpi.delta >= 0 ? 'bg-[#2f8350]/10 text-[#2f8350]' : 'bg-[#b5432f]/10 text-[#b5432f]'">
                            <span x-text="kpi.delta >= 0 ? '▲' : '▼'" aria-hidden="true" class="text-[9px]"></span>
                            <span x-text="(kpi.delta >= 0 ? '+' : '') + kpi.delta + '%'"></span>
                        </span>
                    </template>
                </div>
                <div class="mt-4 flex items-end justify-between gap-4">
                    <p class="text-3xl font-semibold tracking-tight" x-text="number(kpi.value)"></p>
                    <template x-if="kpi.trend">
                        <svg viewBox="0 0 120 32" class="h-8 w-24 shrink-0 overflow-visible" aria-hidden="true">
                            <polyline :points="spark(kpi.trend)" fill="none" stroke="#c8c3b9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <circle :cx="sparkEnd(kpi.trend)[0]" :cy="sparkEnd(kpi.trend)[1]" r="3.5" fill="#9a6f24" stroke="#fff" stroke-width="2" />
                        </svg>
                    </template>
                </div>
            </article>
        </template>
    </div>

    {{-- Enquiries chart + status --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-12">
        <section class="admin-card xl:col-span-8" aria-labelledby="chart-title">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 id="chart-title" class="font-semibold">Enquiries received</h3>
                    <p class="mt-1 text-sm text-smoke" x-text="ranges[range]"></p>
                </div>
                <div class="flex items-center gap-4">
                    <p class="text-2xl font-semibold tracking-tight" x-text="number(total)"></p>
                    <button type="button" @click="showTable = !showTable" :aria-pressed="showTable"
                            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium ring-1 ring-ink/10 transition hover:ring-ink/30">
                        <x-icon name="table-2" /> <span x-text="showTable ? 'Chart' : 'Table'">Table</span>
                    </button>
                </div>
            </div>

            <div x-show="!showTable" class="relative mt-6 h-72" data-admin-chart data-format="number" data-label="enquiries" data-range="{{ $defaultRange }}" data-series='@json($series)'>
                <noscript><p class="text-sm text-smoke">Enable JavaScript to view the chart.</p></noscript>
            </div>

            <div x-cloak x-show="showTable" class="mt-6 max-h-72 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white text-left text-xs text-smoke">
                        <tr><th class="py-2 font-medium">Period</th><th class="py-2 text-right font-medium">Enquiries</th></tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 tabular-nums">
                        <template x-for="point in series[range]" :key="point.label">
                            <tr><td class="py-2" x-text="point.label"></td><td class="py-2 text-right" x-text="point.value"></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-card xl:col-span-4" aria-labelledby="status-title">
            <div class="flex items-start justify-between">
                <div>
                    <h3 id="status-title" class="font-semibold">Enquiries by status</h3>
                    <p class="mt-1 text-sm text-smoke">All time</p>
                </div>
                <x-icon name="inbox" class="text-xl text-brass" />
            </div>
            <x-admin.bar-list :items="$statusCounts" class="mt-6" />
        </section>
    </div>

    {{-- Recent enquiries + services --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-12">
        <section class="overflow-hidden rounded-2xl bg-white ring-1 ring-ink/[0.06] xl:col-span-8" aria-labelledby="recent-title">
            <div class="flex items-center justify-between p-5 lg:px-7 lg:pt-7">
                <h3 id="recent-title" class="font-semibold">Recent enquiries</h3>
                <a href="{{ route('admin.enquiries.index') }}" class="text-xs font-semibold text-ink/60 hover:text-ink">View all</a>
            </div>
            @if ($recentEnquiries->isEmpty())
                <p class="border-t border-ink/5 px-7 py-12 text-center text-sm text-smoke">No enquiries yet. Submissions from the website’s contact form will appear here.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-sm">
                        <thead class="border-y border-ink/5 bg-[#faf8f4] text-left text-xs text-smoke">
                            <tr>
                                <th class="px-5 py-3 font-medium lg:px-7">From</th>
                                <th class="px-3 py-3 font-medium">Service</th>
                                <th class="px-3 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 text-right font-medium lg:px-7">Received</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink/5">
                            @foreach ($recentEnquiries as $enquiry)
                                <tr class="transition hover:bg-[#faf8f4]">
                                    <td class="px-5 py-3.5 lg:px-7">
                                        <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="font-semibold hover:underline">{{ $enquiry->name }}</a>
                                        <p class="text-xs text-smoke">{{ $enquiry->email }}</p>
                                    </td>
                                    <td class="px-3 py-3.5">{{ $enquiry->subject }}</td>
                                    <td class="px-3 py-3.5"><x-admin.status-badge :tone="$enquiry->status_tone" :label="$enquiry->status_label" /></td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap text-smoke lg:px-7">{{ $enquiry->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="admin-card xl:col-span-4" aria-labelledby="services-title">
            <h3 id="services-title" class="font-semibold">Most requested services</h3>
            <p class="mt-1 text-sm text-smoke">All time</p>
            @if (count($serviceCounts))
                <x-admin.bar-list :items="$serviceCounts" class="mt-6" />
            @else
                <p class="mt-6 text-sm text-smoke">Appears once enquiries arrive.</p>
            @endif
        </section>
    </div>

    {{-- Latest products --}}
    <section class="admin-card mt-4" aria-labelledby="products-title">
        <div class="flex items-center justify-between">
            <h3 id="products-title" class="font-semibold">Latest products</h3>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-ink/60 hover:text-ink">Manage products</a>
        </div>
        <ul class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($latestProducts as $product)
                @php $card = $product->toCatalogArray(); @endphp
                <li>
                    <a href="{{ route('admin.products.edit', $product) }}" class="group flex items-center gap-3 xl:block">
                        <img src="{{ asset($card['image']) }}" alt="" class="h-16 w-14 shrink-0 rounded-md object-cover xl:h-auto xl:w-full xl:aspect-[4/5]" loading="lazy">
                        <span class="min-w-0 xl:mt-3 xl:block">
                            <span class="block truncate text-sm font-medium group-hover:underline">{{ $product->name }}</span>
                            <span class="block text-xs text-smoke">{{ $card['price_label'] }} · {{ $product->is_active ? 'Visible' : 'Hidden' }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
</div>
@endsection
