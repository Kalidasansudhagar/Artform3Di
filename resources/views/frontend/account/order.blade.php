@extends('layouts.app')

@section('title', 'Order '.$order->number)
@section('description', 'Your ARTFORM 3Di order.')
@section('header_theme', 'light')

@section('content')
<section class="bg-paper pt-[calc(var(--header-h)+4rem)] pb-24 lg:pb-32">
    <div class="container-x">
        <div class="border-b border-ink/10 pb-12">
            <a href="{{ route('account') }}#orders" class="flex w-fit items-center gap-2 text-sm text-ink/60 transition hover:text-ink"><x-icon name="arrow-left" /> My orders</a>
            @if (session('placed'))
                <p class="eyebrow mt-8 text-smoke">Thank you</p>
                <h1 class="display mt-6 text-display-2">Order <em>placed.</em></h1>
                <p class="mt-6 max-w-xl leading-relaxed text-ink/65">We’ve received order {{ $order->number }}. Our team will call or email you to confirm the finish, delivery and payment before we start printing.</p>
            @else
                <p class="eyebrow mt-8 text-smoke">Placed {{ $order->created_at->format('j M Y') }}</p>
                <h1 class="display mt-6 text-display-2">Order <em>{{ $order->number }}</em></h1>
            @endif
        </div>

        <div class="mt-12 grid gap-12 lg:grid-cols-12 lg:gap-16">
            <ul class="divide-y divide-ink/10 border-y border-ink/10 lg:col-span-7" aria-label="Items">
                @foreach ($order->items as $item)
                    <li class="flex items-center gap-5 py-5">
                        <span class="media-frame relative block aspect-[4/5] w-16 shrink-0">
                            @if ($item->image)<img src="{{ asset($item->image) }}" alt="" width="1200" height="1500" class="absolute inset-0 h-full w-full object-cover" loading="lazy">@endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold">{{ $item->product_name }}</p>
                            <p class="text-sm text-ink/60">Qty {{ $item->quantity }} × {{ \App\Models\Order::money($item->unit_price) }}</p>
                        </div>
                        <p class="tabular-nums">{{ \App\Models\Order::money($item->line_total) }}</p>
                    </li>
                @endforeach
            </ul>

            <aside class="space-y-px bg-ink/10 lg:col-span-5">
                <div class="bg-bone p-6 sm:p-8">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-6"><dt class="text-ink/60">Order</dt><dd>{{ $order->number }}</dd></div>
                        <div class="flex justify-between gap-6"><dt class="text-ink/60">Status</dt><dd class="mono text-brass">{{ $order->status_label }}</dd></div>
                        <div class="flex justify-between gap-6"><dt class="text-ink/60">Payment</dt><dd class="text-right">{{ $order->payment_label }}</dd></div>
                        <div class="flex justify-between gap-6 border-t border-ink/10 pt-4"><dt class="font-semibold">Total</dt><dd class="display text-2xl tabular-nums">{{ \App\Models\Order::money($order->total) }}</dd></div>
                    </dl>
                </div>
                <div class="bg-bone p-6 sm:p-8">
                    <p class="mono text-smoke">Delivering to</p>
                    <p class="mt-3 font-semibold">{{ $order->customer_name }}</p>
                    <p class="mt-1 text-sm leading-relaxed text-ink/70">{!! collect($order->address_lines)->map(fn ($l) => e($l))->implode('<br>') !!}<br>{{ $order->customer_phone }}</p>
                    @if ($order->notes)
                        <p class="mono mt-6 text-smoke">Notes</p>
                        <p class="mt-2 text-sm whitespace-pre-line text-ink/70">{{ $order->notes }}</p>
                    @endif
                </div>
            </aside>
        </div>

        <div class="mt-12 flex flex-col gap-3 sm:flex-row">
            <x-button :href="route('shop.index')" variant="dark">Continue shopping</x-button>
            <x-button :href="route('contact').'#enquiry'" variant="outline-dark" icon="arrow-right">Questions about this order?</x-button>
        </div>
    </div>
</section>
@endsection
