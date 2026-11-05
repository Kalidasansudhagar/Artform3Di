@extends('layouts.admin')

@section('title', 'Dashboard')

{{--
    Admin dashboard preview. All figures come from App\Support\AdminDemo (sample data).
    The range filter re-renders the KPI row and the revenue chart together.
--}}

@section('content')
<div x-data="{
        range: @js($defaultRange),
        ranges: @js($ranges),
        kpis: @js($kpis),
        series: @js($series),
        showTable: false,
        greeting() { const h = new Date().getHours(); return h < 12 ? 'Good morning' : h < 17 ? 'Good afternoon' : 'Good evening'; },
        money(v) { return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', notation: v >= 100000 ? 'compact' : 'standard', minimumFractionDigits: 0, maximumFractionDigits: v >= 100000 ? 1 : 0 }).format(v); },
        format(kpi) { return kpi.format === 'currency' ? this.money(kpi.value) : new Intl.NumberFormat('en-IN').format(kpi.value); },
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
            <h2 class="display mt-1 text-4xl lg:text-5xl"><span x-text="greeting()">Welcome</span>, <em>Admin.</em></h2>
            <p class="mt-2 text-sm text-ink/60">Here’s how the studio and shop are performing.</p>
        </div>
        <div class="flex gap-2">
            <x-button variant="outline-dark" size="sm" icon="download" @click="$store.ui.notify('Reports export arrives in the next phase.')">Export</x-button>
            <x-button variant="dark" size="sm" icon="plus" @click="$store.ui.notify('Product management arrives in the next phase.')">Add product</x-button>
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
        <span class="text-xs text-smoke">Compared with the previous period</span>
    </div>

    {{-- KPI row --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <template x-for="(kpi, i) in kpis[range]" :key="kpi.label">
            <article class="rounded-2xl bg-white p-5 ring-1 ring-ink/[0.06] lg:p-6">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm text-ink/60" x-text="kpi.label"></p>
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                          :class="kpi.delta >= 0 ? 'bg-[#2f8350]/10 text-[#2f8350]' : 'bg-[#b5432f]/10 text-[#b5432f]'">
                        <span x-text="kpi.delta >= 0 ? '▲' : '▼'" aria-hidden="true" class="text-[9px]"></span>
                        <span x-text="(kpi.delta >= 0 ? '+' : '') + kpi.delta + '%'"></span>
                    </span>
                </div>
                <div class="mt-4 flex items-end justify-between gap-4">
                    <p class="text-3xl font-semibold tracking-tight" x-text="format(kpi)"></p>
                    <svg viewBox="0 0 120 32" class="h-8 w-24 shrink-0 overflow-visible" aria-hidden="true">
                        <polyline :points="spark(kpi.trend)" fill="none" stroke="#c8c3b9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <circle :cx="sparkEnd(kpi.trend)[0]" :cy="sparkEnd(kpi.trend)[1]" r="3.5" fill="#9a6f24" stroke="#fff" stroke-width="2" />
                    </svg>
                </div>
            </article>
        </template>
    </div>

    {{-- Revenue + order status --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-12">
        <section class="rounded-2xl bg-white p-5 ring-1 ring-ink/[0.06] lg:p-7 xl:col-span-8" aria-labelledby="revenue-title">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 id="revenue-title" class="font-semibold">Revenue</h3>
                    <p class="mt-1 text-sm text-smoke" x-text="ranges[range]"></p>
                </div>
                <div class="flex items-center gap-4">
                    <p class="text-2xl font-semibold tracking-tight" x-text="money(total)"></p>
                    <button type="button" @click="showTable = !showTable" :aria-pressed="showTable"
                            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium ring-1 ring-ink/10 transition hover:ring-ink/30">
                        <x-icon name="table-2" /> <span x-text="showTable ? 'Chart' : 'Table'">Table</span>
                    </button>
                </div>
            </div>

            <div x-show="!showTable" class="relative mt-6 h-72" data-admin-chart data-range="{{ $defaultRange }}" data-series='@json($series)'>
                <noscript><p class="text-sm text-smoke">Enable JavaScript to view the chart.</p></noscript>
            </div>

            <div x-cloak x-show="showTable" class="mt-6 max-h-72 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white text-left text-xs text-smoke">
                        <tr><th class="py-2 font-medium">Period</th><th class="py-2 text-right font-medium">Revenue</th></tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 tabular-nums">
                        <template x-for="point in series[range]" :key="point.label">
                            <tr><td class="py-2" x-text="point.label"></td><td class="py-2 text-right" x-text="money(point.value)"></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 ring-1 ring-ink/[0.06] lg:p-7 xl:col-span-4" aria-labelledby="status-title">
            <div class="flex items-start justify-between">
                <div>
                    <h3 id="status-title" class="font-semibold">Orders by status</h3>
                    <p class="mt-1 text-sm text-smoke">{{ array_sum(array_column($statusCounts, 'count')) }} orders this month</p>
                </div>
                <x-icon name="package" class="text-xl text-brass" />
            </div>
            <x-admin.bar-list :items="$statusCounts" class="mt-6" />
        </section>
    </div>

    {{-- Recent orders + enquiries --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-12">
        <section class="overflow-hidden rounded-2xl bg-white ring-1 ring-ink/[0.06] xl:col-span-8" aria-labelledby="orders-title">
            <div class="flex items-center justify-between p-5 lg:px-7 lg:pt-7">
                <h3 id="orders-title" class="font-semibold">Recent orders</h3>
                <span class="text-xs text-smoke" title="Order management arrives in the next phase">View all · soon</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead class="border-y border-ink/5 bg-[#faf8f4] text-left text-xs text-smoke">
                        <tr>
                            <th class="px-5 py-3 font-medium lg:px-7">Order</th>
                            <th class="px-3 py-3 font-medium">Product</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 text-right font-medium lg:px-7">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5">
                        @foreach ($orders as $order)
                            <tr class="transition hover:bg-[#faf8f4]">
                                <td class="px-5 py-3.5 whitespace-nowrap lg:px-7">
                                    <p class="font-semibold">{{ $order['number'] }}</p>
                                    <p class="text-xs text-smoke">{{ $order['customer'] }} · {{ $order['when'] }}</p>
                                </td>
                                <td class="px-3 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset($order['image']) }}" alt="" width="40" height="50" loading="lazy" class="h-10 w-8 shrink-0 rounded object-cover">
                                        <div class="min-w-0">
                                            <p class="truncate">{{ $order['product'] }}</p>
                                            <p class="text-xs text-smoke">Qty {{ $order['qty'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3.5"><x-admin.status-badge :tone="$order['tone']" :label="$order['status']" /></td>
                                <td class="px-5 py-3.5 text-right font-semibold whitespace-nowrap tabular-nums lg:px-7">₹{{ number_format($order['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 ring-1 ring-ink/[0.06] lg:p-7 xl:col-span-4" aria-labelledby="enquiries-title">
            <div class="flex items-center justify-between">
                <h3 id="enquiries-title" class="font-semibold">Latest enquiries</h3>
                <span class="rounded-full bg-ink px-2.5 py-0.5 text-xs font-semibold text-paper">{{ collect($enquiries)->where('new', true)->count() }} new</span>
            </div>
            <ul class="mt-5 divide-y divide-ink/5">
                @foreach ($enquiries as $enquiry)
                    <li class="py-4 first:pt-0 last:pb-0">
                        <div class="flex items-center justify-between gap-3">
                            <p class="flex items-center gap-2 text-sm font-semibold">
                                @if ($enquiry['new'])<span class="h-2 w-2 rounded-full bg-brass" title="New"></span>@endif
                                {{ $enquiry['name'] }}
                            </p>
                            <span class="shrink-0 text-xs text-smoke">{{ $enquiry['when'] }}</span>
                        </div>
                        <p class="mt-1 text-xs font-medium tracking-wide text-brass uppercase">{{ $enquiry['type'] }}</p>
                        <p class="mt-1.5 line-clamp-2 text-sm text-ink/60">{{ $enquiry['excerpt'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    {{-- Category sales + top products --}}
    <div class="mt-4 grid gap-4 xl:grid-cols-12">
        <section class="rounded-2xl bg-white p-5 ring-1 ring-ink/[0.06] lg:p-7 xl:col-span-5" aria-labelledby="category-title">
            <h3 id="category-title" class="font-semibold">Sales by category</h3>
            <p class="mt-1 text-sm text-smoke">Revenue, last 30 days</p>
            <x-admin.bar-list :items="$categorySales" currency class="mt-6" />
        </section>

        <section class="rounded-2xl bg-white p-5 ring-1 ring-ink/[0.06] lg:p-7 xl:col-span-7" aria-labelledby="top-title">
            <div class="flex items-center justify-between">
                <h3 id="top-title" class="font-semibold">Top products</h3>
                <a href="{{ route('shop.index') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs text-smoke transition hover:text-ink">View shop <x-icon name="external-link" /></a>
            </div>
            <ol class="mt-5 divide-y divide-ink/5">
                @foreach ($topProducts as $i => $row)
                    <li class="flex items-center gap-4 py-3 first:pt-0 last:pb-0">
                        <span class="w-5 text-sm text-smoke tabular-nums">{{ $i + 1 }}</span>
                        <img src="{{ asset($row['product']['image']) }}" alt="" width="48" height="60" loading="lazy" class="h-12 w-10 shrink-0 rounded-md object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $row['product']['name'] }}</p>
                            <p class="text-xs text-smoke">{{ $row['product']['category_name'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold tabular-nums">{{ $row['units'] }} sold</p>
                            <p class="text-xs text-smoke tabular-nums">₹{{ number_format($row['units'] * $row['product']['price']) }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>
    </div>
</div>
@endsection
