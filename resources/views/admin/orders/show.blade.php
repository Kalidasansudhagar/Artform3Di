@extends('layouts.admin')

@section('title', 'Order '.$order->number)

@php
    $phoneDigits = \App\Support\Contact::phoneDigits($order->customer_phone);
    $whatsapp = \App\Support\Contact::whatsappUrl($order->customer_phone);
@endphp

@section('content')
<a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm text-ink/60 hover:text-ink"><x-icon name="arrow-left" /> All orders</a>

<div class="mt-6 grid gap-4 xl:grid-cols-12">
    <div class="space-y-4 xl:col-span-8">
        <section class="admin-card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="mono text-smoke">{{ $order->payment_label }}</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight">Order {{ $order->number }}</h2>
                    <p class="mt-1 text-sm text-smoke">Placed {{ $order->created_at->format('j M Y, g:i a') }} · {{ $order->created_at->diffForHumans() }}</p>
                </div>
                <x-admin.status-badge :tone="$order->status_tone" :label="$order->status_label" />
            </div>

            <div class="mt-8 overflow-x-auto">
                <table class="w-full min-w-[520px] text-sm">
                    <thead class="border-b border-ink/5 text-left text-xs text-smoke">
                        <tr>
                            <th class="py-3 font-medium">Product</th>
                            <th class="px-3 py-3 text-right font-medium">Price</th>
                            <th class="px-3 py-3 text-right font-medium">Qty</th>
                            <th class="py-3 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($item->image)<img src="{{ asset($item->image) }}" alt="" class="h-12 w-10 rounded-md object-cover" width="40" height="48">@endif
                                        <div>
                                            <p class="font-semibold">{{ $item->product_name }}</p>
                                            @if ($item->product_id)
                                                <a href="{{ route('admin.products.edit', $item->product_id) }}" class="text-xs text-smoke hover:underline">Edit product</a>
                                            @else
                                                <p class="text-xs text-smoke">Product since deleted</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ \App\Models\Order::money($item->unit_price) }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $item->quantity }}</td>
                                <td class="py-3 text-right font-semibold tabular-nums">{{ \App\Models\Order::money($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-ink/10">
                        <tr><td colspan="3" class="py-3 text-right text-smoke">Total</td><td class="py-3 text-right text-lg font-semibold tabular-nums">{{ \App\Models\Order::money($order->total) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            @if ($order->notes)
                <p class="mt-6 text-xs text-smoke">Customer notes</p>
                <div class="mt-2 rounded-xl bg-[#faf8f4] p-4 text-sm leading-relaxed whitespace-pre-line">{{ $order->notes }}</div>
            @endif
        </section>

        <section class="admin-card">
            <h3 class="font-semibold">Customer &amp; delivery</h3>
            <dl class="mt-5 grid gap-6 sm:grid-cols-3">
                <div>
                    <dt class="text-xs text-smoke">Name</dt>
                    <dd class="mt-1 text-sm">{{ $order->customer_name }}</dd>
                    @if ($order->user)<dd class="text-xs text-smoke">Account: {{ $order->user->name }}</dd>@endif
                </div>
                <div>
                    <dt class="text-xs text-smoke">Contact</dt>
                    <dd class="mt-1 text-sm break-all"><a href="mailto:{{ $order->customer_email }}" class="hover:underline">{{ $order->customer_email }}</a></dd>
                    <dd class="text-sm"><a href="tel:+{{ $phoneDigits }}" class="hover:underline">{{ $order->customer_phone }}</a></dd>
                </div>
                <div>
                    <dt class="text-xs text-smoke">Delivery address</dt>
                    <dd class="mt-1 text-sm leading-relaxed">{!! collect($order->address_lines)->map(fn ($l) => e($l))->implode('<br>') !!}</dd>
                </div>
            </dl>
            <div class="mt-6 flex flex-wrap gap-2 border-t border-ink/5 pt-6">
                <x-button href="mailto:{{ $order->customer_email }}?subject={{ rawurlencode('Your Artform 3Di order '.$order->number) }}" variant="dark" size="sm" icon="mail">Email customer</x-button>
                @if ($whatsapp)
                    <x-button :href="$whatsapp" target="_blank" rel="noopener" variant="outline-dark" size="sm" icon="send">WhatsApp</x-button>
                    <x-button href="tel:+{{ $phoneDigits }}" variant="outline-dark" size="sm" icon="phone">Call</x-button>
                @endif
            </div>
        </section>
    </div>

    <aside class="xl:col-span-4">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="admin-card space-y-5">
            @csrf
            @method('PUT')
            <h3 class="font-semibold">Manage</h3>
            <div>
                <label for="status" class="text-sm font-medium">Status</label>
                <select id="status" name="status" class="admin-input mt-2">
                    @foreach (\App\Models\Order::STATUSES as $key => [$label])
                        <option value="{{ $key }}" @selected(old('status', $order->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-smoke">The customer sees this status in My Account.</p>
            </div>
            <x-admin.input name="admin_notes" label="Internal notes" type="textarea" rows="6" :value="old('admin_notes', $order->admin_notes)" hint="Only visible to admins." />
            <button type="submit" class="btn btn-dark w-full"><span>Save changes</span><span class="btn-icon"><x-icon name="check" /></span></button>
        </form>
    </aside>
</div>
@endsection
