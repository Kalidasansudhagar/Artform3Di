@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<x-admin.page-header title="Orders" description="Orders placed through the website shop. No payment is taken online — confirm each order with the customer, then update its status." />

{{-- Status filter + search --}}
<div class="mt-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <nav class="-mx-4 flex gap-1 overflow-x-auto px-4 [scrollbar-width:none] sm:mx-0 sm:px-0" aria-label="Filter by status">
        @php $total = $counts->sum(); @endphp
        <a href="{{ route('admin.orders.index', array_filter(['q' => $search])) }}" @class(['shrink-0 rounded-full px-4 py-2 text-xs font-medium transition', 'bg-ink text-paper' => $status === '', 'bg-white ring-1 ring-ink/10 hover:ring-ink/30' => $status !== ''])>All <span class="opacity-60">{{ $total }}</span></a>
        @foreach (\App\Models\Order::STATUSES as $key => [$label])
            <a href="{{ route('admin.orders.index', array_filter(['status' => $key, 'q' => $search])) }}" @class(['shrink-0 rounded-full px-4 py-2 text-xs font-medium whitespace-nowrap transition', 'bg-ink text-paper' => $status === $key, 'bg-white ring-1 ring-ink/10 hover:ring-ink/30' => $status !== $key])>{{ $label }} <span class="opacity-60">{{ $counts[$key] ?? 0 }}</span></a>
        @endforeach
    </nav>
    <form method="GET" class="relative w-full lg:w-80">
        @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <label for="q" class="sr-only">Search orders</label>
        <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-smoke" />
        <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Search order no., name, email, phone…" class="admin-input pl-10">
    </form>
</div>

<section class="mt-6 overflow-hidden rounded-2xl bg-white ring-1 ring-ink/[0.06]">
    @if ($orders->isEmpty())
        <div class="px-6 py-20 text-center">
            <x-icon name="package" class="text-3xl text-brass" />
            <p class="mt-4 font-semibold">{{ $search || $status ? 'No orders match these filters.' : 'No orders yet.' }}</p>
            <p class="mt-1 text-sm text-smoke">Orders placed from the website shop appear here.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="border-b border-ink/5 bg-[#faf8f4] text-left text-xs text-smoke">
                    <tr>
                        <th class="px-5 py-3 font-medium lg:px-7">Order</th>
                        <th class="px-3 py-3 font-medium">Customer</th>
                        <th class="px-3 py-3 text-right font-medium">Items</th>
                        <th class="px-3 py-3 text-right font-medium">Total</th>
                        <th class="px-3 py-3 font-medium">Payment</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-right font-medium lg:px-7">Placed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5">
                    @foreach ($orders as $order)
                        <tr @class(['transition hover:bg-[#faf8f4]', 'bg-[#c08417]/[0.04]' => $order->status === 'pending'])>
                            <td class="px-5 py-3.5 whitespace-nowrap lg:px-7"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold hover:underline">{{ $order->number }}</a></td>
                            <td class="px-3 py-3.5 whitespace-nowrap">
                                {{ $order->customer_name }}
                                <p class="text-xs text-smoke">{{ $order->customer_phone }}</p>
                            </td>
                            <td class="px-3 py-3.5 text-right tabular-nums">{{ $order->units }}</td>
                            <td class="px-3 py-3.5 text-right font-semibold tabular-nums">{{ \App\Models\Order::money($order->total) }}</td>
                            <td class="px-3 py-3.5 whitespace-nowrap text-ink/70">{{ $order->payment_method === 'cod' ? 'Cash on delivery' : 'UPI / bank' }}</td>
                            <td class="px-3 py-3.5"><x-admin.status-badge :tone="$order->status_tone" :label="$order->status_label" /></td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap text-smoke lg:px-7" title="{{ $order->created_at->format('j M Y, g:i a') }}">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

{{ $orders->links('admin.partials.pagination') }}
@endsection
