@extends('layouts.app')

@section('title', 'Your bag')
@section('description', 'Review the pieces in your ARTFORM 3Di bag.')
@section('header_theme', 'light')

@section('content')
<section class="bg-paper pt-[calc(var(--header-h)+4rem)] pb-24 lg:pb-32">
    <div class="container-x">
        <div class="border-b border-ink/10 pb-12">
            <p class="eyebrow text-smoke">Shopping bag</p>
            <h1 class="display mt-6 text-display-2">Your <em>bag.</em></h1>
        </div>

        @if ($items->isEmpty())
            <div class="mt-12 flex flex-col items-center border border-dashed border-ink/15 px-6 py-20 text-center">
                <x-icon name="shopping-bag" class="text-3xl text-brass" />
                <p class="display mt-6 text-3xl">Your bag is empty</p>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink/60">Explore the collection and add the pieces you like.</p>
                <x-button :href="route('shop.index')" variant="dark" class="mt-8">Explore the collection</x-button>
            </div>
        @else
            <div class="mt-12 grid gap-12 lg:grid-cols-12 lg:gap-16">
                <ul class="divide-y divide-ink/10 border-y border-ink/10 lg:col-span-8" aria-label="Items in your bag">
                    @foreach ($items as $line)
                        @php $product = $line['product']; @endphp
                        <li class="flex gap-5 py-6 sm:gap-8">
                            <a href="{{ $product['url'] }}" class="media-frame block aspect-[4/5] w-24 shrink-0 sm:w-32">
                                <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }}" width="1200" height="1500" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                            </a>
                            <div class="flex min-w-0 flex-1 flex-col">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-[11px] tracking-[0.18em] text-smoke uppercase">{{ $product['category_name'] }}</p>
                                        <h2 class="display mt-2 text-2xl leading-tight"><a href="{{ $product['url'] }}">{{ $product['name'] }}</a></h2>
                                        <p class="mt-1 text-sm text-ink/60">{{ $product['price_label'] }} each</p>
                                    </div>
                                    <p class="shrink-0 tabular-nums">{{ \App\Models\Order::money($line['line_total']) }}</p>
                                </div>

                                <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-5">
                                    <form method="POST" action="{{ route('cart.update', $line['id']) }}" class="flex h-11 items-center justify-between rounded-full px-1.5 ring-1 ring-ink/15">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="quantity" value="{{ $line['quantity'] - 1 }}" class="icon-btn h-8 w-8" aria-label="Decrease quantity of {{ $product['name'] }}"><x-icon name="minus" /></button>
                                        <span class="w-8 text-center tabular-nums" aria-label="Quantity">{{ $line['quantity'] }}</span>
                                        <button type="submit" name="quantity" value="{{ min(\App\Support\Cart::MAX_QTY, $line['quantity'] + 1) }}" class="icon-btn h-8 w-8" aria-label="Increase quantity of {{ $product['name'] }}"><x-icon name="plus" /></button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $line['id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="link-line text-xs font-semibold tracking-[0.14em] uppercase">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <aside class="lg:col-span-4">
                    <div class="bg-bone p-6 sm:p-8 lg:sticky lg:top-[calc(var(--header-h)+2rem)]">
                        <h2 class="display text-3xl">Summary</h2>
                        <dl class="mt-6 divide-y divide-ink/10 border-y border-ink/10 text-sm">
                            <div class="flex justify-between gap-6 py-3"><dt class="text-ink/60">Subtotal</dt><dd class="tabular-nums">{{ \App\Models\Order::money($subtotal) }}</dd></div>
                            <div class="flex justify-between gap-6 py-3"><dt class="text-ink/60">Delivery</dt><dd class="text-right">Confirmed with your order</dd></div>
                        </dl>
                        <div class="mt-5 flex items-baseline justify-between gap-6">
                            <p class="font-semibold">Total</p>
                            <p class="display text-3xl tabular-nums">{{ \App\Models\Order::money($subtotal) }}</p>
                        </div>
                        <x-button :href="route('checkout')" variant="dark" size="lg" icon="arrow-right" class="mt-8 w-full">Checkout</x-button>
                        @guest
                            <p class="mt-4 text-center text-xs text-ink/60">You’ll be asked to sign in or create an account.</p>
                        @endguest
                        <a href="{{ route('shop.index') }}" class="link-line is-static mt-6 inline-block text-sm text-ink">Continue shopping</a>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</section>
@endsection
